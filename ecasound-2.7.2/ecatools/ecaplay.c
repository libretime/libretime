/**
 * ecaplay.c: A simple command-line tool for playing audio files.
 *
 * Copyright (C) 1999-2002,2004-2006 Kai Vehmanen
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
 */

/**
 * TODO:
 * - show playlist length during runtime
 * - random start switch (both for cmdline and playlist modes)
 * - write some notes about locking issues
 */

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include <assert.h>
#include <limits.h>
#include <stdio.h>
#include <stdlib.h> /* ANSI-C: malloc(), free() */
#include <string.h> /* ANSI-C: strlen(), strncmp() */

#include <unistd.h>    /* POSIX: ... */
#include <signal.h>    /* POSIX: sigaction() */
#include <sys/stat.h>  /* POSIX: mkdir() */
#include <sys/types.h> /* POSIX: mkdir() */

#include <ecasoundc.h>

/**
 * Function declarations
 */

int main(int argc, char *argv[]);

static void add_input_to_chainsetup(eci_handle_t eci, const char* nextrack);
static int flush_tracks(void);
static const char* get_next_track(int *tracknum, int argc, char *argv[], eci_handle_t *eci);
static char* get_playlist_path(void);
static const char* get_track_cmdline(int n, int argc, char *argv[]);
static const char* get_track_playlist(int* next_track);
static void initialize_chainsetup_for_playback(eci_handle_t* eci, const char* nexttrack, int tracknum);
static void initialize_check_output(eci_handle_t* eci);
static int list_tracks(void);
static int play_tracks(int argc, char *argv[]);
static void print_usage(FILE* stream);
static int process_option(const char* option);
static int queue_tracks(int argc, char *argv[]);
static int set_audio_format(eci_handle_t* eci, const char* fmt);
static void setup_signal_handling(void);
static void signal_handler(int signum);

/**
 * Definitions and options 
 */

#define ECAPLAY_AFMT_MAXLEN     64
#define ECAPLAY_EIAM_LOGLEVEL   256
#define ECAPLAY_TIMEOUT         3

#define ECAPLAY_MODE_NORMAL     0
#define ECAPLAY_MODE_PL_FLUSH   1
#define ECAPLAY_MODE_PL_LIST    2
#define ECAPLAY_MODE_PL_PLAY    3
#define ECAPLAY_MODE_PL_QUEUE   4

#define ECAPLAY_PLAYLIST_BASEDIR    ".ecasound"
#define ECAPLAY_PLAYLIST_FILE       "ecaplay_queue"

/** 
 * Global variables
 */

static const char* ecaplay_version = "20061206-45";    /* ecaplay version */
static char ecaplay_next[PATH_MAX];                    /* file to play next */
static char ecaplay_audio_format[ECAPLAY_AFMT_MAXLEN]; /* audio format to use */
static const char* ecaplay_output = NULL;              /* output device to use */
static int ecaplay_debuglevel = ECAPLAY_EIAM_LOGLEVEL; /* debug level to use */
static int ecaplay_skip = 0;                           /* how many playlist items to skip */
static int ecaplay_mode = ECAPLAY_MODE_NORMAL;         /* playlist mode */
/* FIX: static int ecaplay_list_len = -1;                   playlist length */
static int ecaplay_initialized = 0;                    /* playlist mode */
static sig_atomic_t ecaplay_skip_flag = 0;             /* signal flag for ctrl-c */

/**
 * Function definitions
 */

int main(int argc, char *argv[])
{
  int i, res = 0;

  /* get the default output device */
  ecaplay_output = getenv("ECAPLAY_OUTPUT_DEVICE");

  /* process command-line arguments */
  for(i = 1; i < argc; i++) { res += process_option(argv[i]); }

  if (res == 0) {
    switch(ecaplay_mode) {
    case ECAPLAY_MODE_PL_FLUSH:
      res = flush_tracks();
      break;
      
    case ECAPLAY_MODE_PL_LIST:
      res = list_tracks();
      break;
      
    case ECAPLAY_MODE_PL_QUEUE:
      res = queue_tracks(argc, argv);
      break;
      
    case ECAPLAY_MODE_NORMAL:
    case ECAPLAY_MODE_PL_PLAY:
      res = play_tracks(argc, argv);
      break;
      
    default:
      assert(0);
    }
  }

  if (res != 0) {
    fprintf(stderr, "(ecaplay) Errors encountered, return code is %d.\n", res);
  }

  return res;
}

/**
 * Adds input 'nexttrack' to currently selected chainsetup
 * of 'eci'. Sets the global variable 'ecaplay_audio_format'.
 */
static void add_input_to_chainsetup(eci_handle_t eci, const char* nexttrack)
{
  size_t len = strlen("ai-add '") + strlen(nexttrack) + strlen("'") + 1;
  char* tmpbuf = malloc(len);

  assert(tmpbuf != NULL);
  snprintf(tmpbuf, len, "ai-add \"%s\"", nexttrack);
  eci_command_r(eci, tmpbuf);

  /* check that add succeeded */
  eci_command_r(eci, "ai-list");
  if (eci_last_string_list_count_r(eci) != 1) {
    fprintf(stderr, "(ecaplay) Warning! Failed to add input '%s'.\n", nexttrack);
  }

  /* we must connect to get correct input format */
  eci_command_r(eci, "ao-add null");
  eci_command_r(eci, "cs-connect");
  eci_command_r(eci, "ai-iselect 1");
  eci_command_r(eci, "ai-get-format");

  strncpy(ecaplay_audio_format, 
	  eci_last_string_r(eci),
	  ECAPLAY_AFMT_MAXLEN);
  ecaplay_audio_format[ECAPLAY_AFMT_MAXLEN - 1] = 0;

  /* disconnect and remove the null output */
  eci_command_r(eci, "cs-disconnect");
  eci_command_r(eci, "ao-iselect 1");
  eci_command_r(eci, "ao-remove");

  free(tmpbuf);
}

/**
 * Flushes the playlist contents.
 *
 * @return zero on success, non-zero otherwise
 */
static int flush_tracks(void)
{
  char *path = get_playlist_path();
  if (truncate(path, 0) != 0) {
    printf("(ecaplay) Unable to flush playlist '%s'.\n", path);
    return -1;
  }
  return 0;
}

/**
 * Checks that current chainsetup has exactly one
 * output.
 */ 
static void initialize_check_output(eci_handle_t* eci)
{
  eci_command_r(eci, "ao-list");
  if (eci_last_string_list_count_r(eci) != 1) {
    fprintf(stderr, "(ecaplay) Warning! Failed to add output device.\n");
  }
  else {
    static int once = 1;
    if (once) {
      eci_command_r(eci, "ao-iselect 1");
      eci_command_r(eci, "ao-describe");
      char *tmpstr = (char*)eci_last_string_r(eci);
      /* skip the "-x:" prefix where x is one of [io] */
      while(*tmpstr && *tmpstr++ != ':') 
	;
      printf("(ecaplay) Output device: '%s'\n", tmpstr);
      once = 0;
    }
  }
}

static void initialize_chainsetup_for_playback(eci_handle_t* eci, const char* nexttrack, int tracknum)
{
  const char* ret = NULL;

  *eci = eci_init_r();
  ecaplay_initialized = 1;

  if (ecaplay_debuglevel != -1) {
    char tmpbuf[32];
    snprintf(tmpbuf, 32, "debug %d", ecaplay_debuglevel);
    eci_command_r(*eci, tmpbuf);
  }

  eci_command_r(*eci, "cs-add ecaplay_chainsetup");
  /* check that add succeeded */
  eci_command_r(*eci, "cs-list");
  if (eci_last_string_list_count_r(*eci) != 2) {
    fprintf(stderr, "(ecaplay) Warning! Failed to add a new chainsetup.\n");
  }

  /* as this is a new chainsetup, we can assume that 
   * adding chains succeeds */
  eci_command_r(*eci, "c-add ecaplay_chain");
  
  add_input_to_chainsetup(*eci, nexttrack);
  set_audio_format(*eci, ecaplay_audio_format);

  if (ecaplay_output == NULL) {
    eci_command_r(*eci, "ao-add-default");

    /* check that add succeeded */
    initialize_check_output(*eci);
  }
  else {
    int len = strlen("ao-add ") + strlen(ecaplay_output) + 1;
    char* tmpbuf = (char*)malloc(len);
    snprintf(tmpbuf, len, "ao-add %s", ecaplay_output);
    eci_command_r(*eci, tmpbuf);
    initialize_check_output(*eci);
    free(tmpbuf);
  }

  /* FIXME: add detection of consecutive errors */

  eci_command_r(*eci, "cs-connect");
  if (eci_error_r(*eci)) {
    fprintf(stderr, "(ecaplay) Unable to play file '%s':\n%s\n", nexttrack, eci_last_error_r(*eci));
  }
  else {
    eci_command_r(*eci, "cs-connected");
    ret = eci_last_string_r(*eci);
    if (strncmp(ret, "ecaplay_chainsetup", strlen("ecaplay_chainsetup")) != 0) {
      fprintf(stderr, "(ecaplay) Error while playing file '%s' . Skipping...\n", nexttrack);
    }
    else {
      /* note: audio format set separately for each input file */
      printf("(ecaplay) Playing %d: '%s' (%s).\n", tracknum, nexttrack, ecaplay_audio_format);
      eci_command_r(*eci, "start");
    }
  }
}

static const char* get_next_track(int *tracknum, int argc, char *argv[], eci_handle_t *eci)
{
  const char *nexttrack = NULL;

  if (ecaplay_mode == ECAPLAY_MODE_PL_PLAY)
    nexttrack = get_track_playlist(tracknum);
  else
    nexttrack = get_track_cmdline(*tracknum, argc, argv);
  
  if (nexttrack != NULL) {
    /* queue nexttrack for playing */
    if (ecaplay_initialized) {
      eci_cleanup_r(*eci);
    }
    initialize_chainsetup_for_playback(eci, nexttrack, *tracknum);
  }
  else {
    /* reached end of playlist */
    if (ecaplay_mode != ECAPLAY_MODE_PL_PLAY) {
      /* normal mode; end processing after all files played */
      /* printf("(ecaplay) No more files...\n"); */
      assert(nexttrack == NULL);
    }
    else {
      /* if in playlist mode, loop from beginning */
      *tracknum = 1;

      /* FIXME: if in playlist mode; query the current lenght of
       *        playlist and set 'tracknum = (tracknum % pllen)' */

      if (ecaplay_mode == ECAPLAY_MODE_PL_PLAY)
	nexttrack = get_track_playlist(tracknum);
      else
	nexttrack = get_track_cmdline(*tracknum, argc, argv);

      /* printf("(ecaplay) Looping back to start of playlist...(%s)\n", nexttrack); */

      if (nexttrack != NULL) {
	/* queue nexttrack for playing */
	if (ecaplay_initialized) {
	  eci_cleanup_r(*eci);
	}
	initialize_chainsetup_for_playback(eci, nexttrack, *tracknum);
      }
      else {
	/* get_next_track() failed two times, stopping processing */
	assert(nexttrack == NULL);
      }
    }
  }

  return nexttrack;
}

/**
 * Returns the track number 'n' from the list 
 * given in argc and argv.
 * 
 * @return track name or NULL on error
 */
static const char* get_track_cmdline(int n, int argc, char *argv[])
{
  int i, c = 0;

  assert(n > 0 && n <= argc);

  for(i = 1; i < argc; i++) { 
    /* FIXME: add support for '-- -foo.wav' */
    if (argv[i][0] != '-') {
      if (++c == n) {
	return argv[i];
      }
    }
  }
 
  return NULL;
}

/**
 * Returns a string containing the full path to the
 * playlist file. Ownership of the string is transfered
 * to the caller (i.e. it must be free()'ed).
 *
 * @return full pathname or NULL if error has occured
 */
static char* get_playlist_path(void)
{
  char *path = malloc(PATH_MAX);
  struct stat statbuf;

  /* create pathname based on HOME */
  strncpy(path, getenv("HOME"), PATH_MAX);
  strncat(path, "/" ECAPLAY_PLAYLIST_BASEDIR, PATH_MAX - strlen(path) - 1);

  /* make sure basedir exists */
  if (stat(path, &statbuf) != 0) {
    printf("(ecaplay) Creating directory %s.\n", path);
    mkdir(path, 0700);
  }
  else {
    if (!S_ISDIR(statbuf.st_mode)) {
      /* error, basedir exists but is not a directory */
      free(path);
      path = NULL;
    }
  }
  
  if (path != NULL) {
    /* add filename to basedir */
    strncat(path, "/" ECAPLAY_PLAYLIST_FILE, PATH_MAX - strlen(path) - 1);
  }

  return path;
}

/**
 * Returns the track from playlist matching number 'next_track'.
 * 
 * In case 'next_track' is larger than the playlist length, 
 * track 'next_track mod playlist_len' will be selected, and
 * the modified playlist item number stored to 'next_track'.
 * 
 * Note: modifies global variable 'ecaplay_next'.
 *
 * @return track name or NULL on error
 */
static const char* get_track_playlist(int* next_track)
{

  const char *res = NULL;
  char *path;
  FILE *f1;
  int next = *next_track;

  assert(next > 0);

  path = get_playlist_path();
  if (path == NULL) {
    return path;
  }

  f1 = fopen(path, "rb");
  if (f1 != NULL) {
    int c, w, cur_item = 1;

    /* iterate through all data octet at a time */
    for(w = 0;;) {
      c = fgetc(f1);
      if (c == EOF) {
	if (next > cur_item) {
	  /* next_track beyond playlist length, reset to valid track number */
	  next = next % cur_item;
	  *next_track = next;
	  /* seek back to start and look again */
	  fseek(f1, 0, SEEK_SET);
	  cur_item = 1;
	  w = 0;
	  continue;
	}
	break;
      }

      if (cur_item == next) {
	if (c == '\n') {
      	  ecaplay_next[w] = 0;
	  res = ecaplay_next;
	  break;
	}
      	else {
	  ecaplay_next[w] = c;
	}
	++w;
      }
      if (c == '\n') {
	++cur_item;
      }
    }

    /* close the file and return results */
    fclose(f1);
  }

  free(path);
  
  return res;
}

/**
 * Lists tracks on the playlist.
 *
 * @return zero on success, non-zero otherwise
 */
static int list_tracks(void)
{
  FILE *f1;
  char *path = get_playlist_path();

  f1 = fopen(path, "rb");
  if (f1 != NULL) {
    int c;
    while((c = fgetc(f1)) != EOF) {
      printf("%c", c);
    }
    fclose(f1);
    return 0;
  }
  return -1;
}

/**
 * Play tracks using the Ecasound engine via the
 * ECI interface.
 *
 * Depending on the mode, tracks are selected either
 * from the command-line or from the playlist.
 */
static int play_tracks(int argc, char *argv[])
{
  eci_handle_t eci = NULL;
  int tracknum = 1, stop = 0;
  const char* nexttrack = NULL;

  assert(ecaplay_mode == ECAPLAY_MODE_NORMAL || 
	 ecaplay_mode == ECAPLAY_MODE_PL_PLAY);

  tracknum += ecaplay_skip;

  nexttrack = get_next_track(&tracknum, argc, argv, &eci);

  if (nexttrack != NULL) {
    setup_signal_handling();

    while(nexttrack != NULL) {
      unsigned int timeleft = ECAPLAY_TIMEOUT;

      while(timeleft > 0) {
	timeleft = sleep(timeleft);

	if (timeleft > 0 && ecaplay_skip_flag > 1) {
	  fprintf(stderr, "\n(ecaplay) Interrupted, exiting...\n");
	  eci_cleanup_r(eci);
	  stop = 1;
	  break;
	}
      }

      /* see above while() loop */
      if (stop) break;

      if (ecaplay_skip_flag == 0) {
	eci_command_r(eci, "engine-status");
      }
      else {
	printf("(ecaplay) Skipping...\n");
      }

      if (ecaplay_skip_flag != 0 || strcmp(eci_last_string_r(eci), "running") != 0) {
	ecaplay_skip_flag = 0;
	++tracknum;
	nexttrack = get_next_track(&tracknum, argc, argv, &eci);
	/* printf("Next track is %s.\n", nexttrack); */
      }
    }
  
    fprintf(stderr, "exiting...\n");

    /* see while() loop above */
    if (stop == 0) {
      eci_cleanup_r(eci);
    }
  }

  return 0;
}

static void print_usage(FILE* stream)
{
  fprintf(stream, "Ecaplay v%s (%s)\n\n", ecaplay_version, VERSION);

  fprintf(stream, "Copyright (C) 1997-2005 Kai Vehmanen, released under GPL licence \n");
  fprintf(stream, "Ecaplay comes with ABSOLUTELY NO WARRANTY.\n");
  fprintf(stream, "You may redistribute copies of ecasound under the terms of the GNU\n");
  fprintf(stream, "General Public License. For more information about these matters, see\n"); 
  fprintf(stream, "the file named COPYING.\n");

  fprintf(stream, "\nUSAGE: ecaplay [-dfhklopq] [ file1 file2 ... fileN ]\n\n");

  fprintf(stream, "See ecaplay(1) man page for more details.\n");
}

static int process_option(const char* option)
{
  if (option[0] == '-') {
    if (strncmp("--help", option, sizeof("--help")) == 0 ||
	strncmp("--version", option, sizeof("--version")) == 0) {
      print_usage(stdout);
      return 0;
    }

    switch(option[1]) 
      {
      case 'd': 
	{
	  const char* level = &option[3];
	  if (option[2] != 0 && option[3] != 0) {
	    ecaplay_debuglevel |= atoi(level);
	    printf("(ecaplay) Setting log level to %d.\n", ecaplay_debuglevel);
	  }
	  break;
	}

      case 'f': 
	{
	  ecaplay_mode = ECAPLAY_MODE_PL_FLUSH;
	  printf("(ecaplay) Flushing playlist.\n");
	  break;
	}

      case 'h': 
	{
	  print_usage(stdout);
	  return 0;
	}
      
      case 'k': 
	{
	  const char* skip = &option[3];
	  if (option[2] != 0 && option[3] != 0) {
	    ecaplay_skip = atoi(skip);
	    printf("(ecaplay) Skipping the first %d files..\n", ecaplay_skip);
	  }
	  break;
	}

      case 'l': 
	{
	  ecaplay_mode = ECAPLAY_MODE_PL_LIST;
	  /* printf("(ecaplay) Listing playlist contents.\n"); */
	  break;
	}
	
      case 'o': 
	{
	  const char* output = &option[3];
	  if (option[2] != 0 && option[3] != 0) {
	    ecaplay_output = output;
	    /* printf("(ecaplay) Output device: '%s'\n", ecaplay_output); */
	  }
	  break;
	}

      case 'p': 
	{
	  ecaplay_mode = ECAPLAY_MODE_PL_PLAY;
	  printf("(ecaplay) Playlist mode selected (file: %s).\n",
		 "~/" ECAPLAY_PLAYLIST_BASEDIR "/" ECAPLAY_PLAYLIST_FILE);
	  break;
	}

      case 'q': 
	{
	  ecaplay_mode = ECAPLAY_MODE_PL_QUEUE;
	  printf("(ecaplay) Queuing tracks to playlist.\n");
	  break;
	}
      
      default:
	{
	  fprintf(stderr, "(ecaplay) Error! Unknown option '%s'.\n", option);
	  print_usage(stderr);
	  return 1;
	}
      }
  }

  return 0;
}

static int queue_tracks(int argc, char *argv[])
{
  int i, res = 0;
  char *path;
  FILE *f1;

  path = get_playlist_path();
  /* path maybe NULL but fopen can handle it */

  f1 = fopen(path, "a+b");
  if (f1 != NULL) {
    for(i = 1; i < argc; i++) { 
      char c = argv[i][0];
      /* printf("(ecaplay) processing arg '%s' (%c).\n", argv[i], c); */
      /* FIXME: add support for '-- -foo.wav' */
      if (c != '-') {
	/* printf("(ecaplay) 2:processing arg '%s' (%c).\n", argv[i], c); */
	if (c != '/') {
	  /* reserve extra room for '/' */
	  char* tmp = malloc(PATH_MAX + strlen(argv[i]) + 1);
	  if (getcwd(tmp, PATH_MAX) != NULL) {
	    strcat(tmp, "/");
	    strcat(tmp, argv[i]);
	    printf("(ecaplay) Track '%s' added to playlist.\n", argv[i]);
	    fwrite(tmp, 1, strlen(tmp), f1);
	  }
	  free(tmp);
	}
	else {
	  printf("(ecaplay) Track '%s' added to playlist.\n", argv[i]);
	  fwrite(argv[i], 1, strlen(argv[i]), f1);
	}
	fwrite("\n", 1, 1, f1);
      }
    }
    fclose(f1);
  }
  else {
    res = -1;
  }

  free(path); /* can be NULL */

  return res;
}

/**
 * Sets the chainsetup audio format to 'fmt'.
 *
 * @return zero on success, non-zero on error
 */
int set_audio_format(eci_handle_t* eci, const char* fmt)
{
  size_t len = strlen("cs-set-audio-format -f:") + strlen(fmt) + 1;
  char* tmpbuf = malloc(len);
  int res = 0;

  strcpy(tmpbuf, "cs-set-audio-format ");
  strcat(tmpbuf, fmt);
  tmpbuf[len - 1] = 0;
  eci_command_r(eci, tmpbuf);
  if (eci_error_r(eci)) {
    fprintf(stderr, "(ecaplay) Unknown audio format encountered.\n");
    res = -1;
  }
  free(tmpbuf);

  return res;
}

static void setup_signal_handling(void)
{
  struct sigaction es_handler_int;
  struct sigaction ign_handler;

  es_handler_int.sa_handler = signal_handler;
  sigemptyset(&es_handler_int.sa_mask);
  es_handler_int.sa_flags = 0;

  ign_handler.sa_handler = SIG_IGN;
  sigemptyset(&ign_handler.sa_mask);
  ign_handler.sa_flags = 0;

  /* handle the follwing signals explicitly */
  sigaction(SIGINT, &es_handler_int, 0);

  /* ignore the following signals */
  sigaction(SIGPIPE, &ign_handler, 0);
  sigaction(SIGFPE, &ign_handler, 0);
}

static void signal_handler(int signum)
{
  ++ecaplay_skip_flag;
}
