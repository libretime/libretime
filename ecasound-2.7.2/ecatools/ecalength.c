/*   
    This is ecalength, a few lines of code pompously named so because they  
    let one retrieve the length of an audio file from the command line  
    using ecasound's engine.  

    Limitations:  
    - It will only work correctly if the audio file is at a sampling rate  
      of 44100 hz, unless the file is a wave file; for other formats such 
      as .au, .raw and .cdr that have a sr other than 44100 the format needs 
      to be specified with the -a switch.
      NOTE: mp3 files do have sr information in their headers but 
      unfortunately ecasound currently seems unable to parse this information 
      correctly. :(
    - It is not foolproof, feeding it with something other than an audio  
    file WILL result in ugly things being spewed back.  
         (A bit better)
    - A thousand more that I haven't thought of.  

    Please post back any improvement you make; I can be reached at:  
    observer@colba.net  

    note: Compile it with:  
    gcc -Wall -o ecalength ecalength.c `libecasoundc-config --cflags --libs`

*    updated: Thu May 10 15:56:18 EDT 2001
- Now works with the new ai/ao scheme.
- Switches implemented, made suitable for scripting.
- Format querying/setting.
- Better error handling.  
*    updated: Wed Nov 14 23:26:19 EST 2001
- New option -su lets us return the file's length in samples.
  (This breaks compatibility with stable series.)
- Reworked the comment above to say that basically only wave files are able 
  to self-adjust.
- Started to wondered whether my nice options structure isn't a bit too 
  unobvious for anyone else than me. (???)
- Help screen's getting a bit long, I have to scrollback to see the error 
  message. (???) (addressed)
*    updated: Thu Nov 15 11:51:35 EST 2001
- Tried to format the code a bit better however hopeless it looks, tried to 
  comment it a bit.
- Tried to catch wrong switches a bit better.
- Only print full help message when no other message is being spewed.
*    updated: Sun Jan  6 14:37:02 EST 2002
- Woo! Ecasound's internals now support quoting, had to take advantage of this.
*    updated: Mon Apr 29 02:41:13 EEST 2002
- Renamed to ecalength.cpp to avoid troubles with linking ecalength 
  against uninstalled libecasoundc.
*    updated: Thu Oct 31 17:41:05 EET 2002
- Renamed to ecalength.c. Updated the compilation instructions.
*/ 

#include <stdio.h> 
#include <unistd.h> 
#include <string.h>
#include <stdlib.h> /* exit() */

#include "ecasoundc.h"

#define FALSE          0 
#define TRUE           1 

void make_human(int length, unsigned int *min, unsigned char *sec); 
void print_help(char* name); 
void print_usage(char* name);

struct options { 
  char adjust;
  char format; 
  char total; 
  char script; 
  char human;
  char bits;
  char ccount; 
  char rate;
  char samples;
};

int main(int argc, char *argv[]) { 
  char cmd[512], fstring[16], status = 0, *optstr = "ftsmhbcra:u"; 
  int curopt, curarg;
  unsigned char sec; 
  float curfilelength, totlength = 0; 
  unsigned int min;
  FILE *file; 
  struct options opts; 

  /* No surprises please */
  opts.adjust = FALSE;
  opts.format = FALSE; 
  opts.total = FALSE; 
  opts.script = FALSE; 
  opts.human = FALSE; 
  opts.bits = FALSE;
  opts.ccount = FALSE;
  opts.rate = FALSE;
  opts.samples = FALSE;

  /* Now let's parse and set. */
  while ((curopt = getopt(argc, argv, optstr)) != -1) { 
    switch (curopt) { 
    case 'a' : opts.adjust = TRUE;
      strcpy(fstring, optarg);
      break;
    case 'f' : opts.format = TRUE; 
      break; 
    case 't' : opts.total = TRUE; 
      break; 
    case 's' : opts.script = TRUE; 
      break; 
    case 'm' : opts.human = TRUE; 
      break; 
    case 'b' : opts.bits = TRUE;
      break;
    case 'c' : opts.ccount = TRUE;
      break;
    case 'r' : opts.rate = TRUE;
      break;
    case 'u' : opts.samples = TRUE;
      break;
    case 'h' : print_help(argv[0]);
      exit(0);
    case '?' : print_usage(argv[0]);
      exit(1);
    } 
  } 

  /* No file? */
  if (argc-optind == 0) {
    print_help(argv[0]);
    exit(1);
  }

  /* Well, let's not just shut up if options are out of context, let's whine 
   * about it a bit so that people know why they're not getting what they 
   * expected. */
  if (!opts.script) {
     /* If not in script mode then we should check and make sure that we warn 
      * if script options have been set. I assume it's fine to spit to stdout 
      * here. */ 
      /* Local string where we store naughty switches. */
      char badopts[10] = "\0";
      
      /* Off we go. */
      if (opts.format) { strcat(badopts, "f"); }
      if (opts.bits) { strcat(badopts, "b"); }
      if (opts.ccount) { strcat(badopts, "c"); }
      if (opts.rate) { strcat(badopts, "r"); }
      if (opts.human) { strcat(badopts, "m"); }
      if (opts.samples) { strcat(badopts, "u"); }
      if (strlen(badopts)) {
        printf("-%s :: Out of context options will be ignored.\n", 
                        badopts); 
      }
  } else {
      /* Now, if we're in script mode we want to make sure of a few things, 
       * we also want to warn on stderr, of course. */
      char badopts[20] = "\0";

      /* The whole format thing is a bit complex so I guess we want to help 
       * out. */
      if (!opts.format) {
         if (opts.bits) { strcat(badopts, "b"); }
         if (opts.ccount) { strcat(badopts, "c"); }
         if (opts.rate) { strcat(badopts, "r"); }
         if (strlen(badopts) == 1) {
            fprintf(stderr, "You can't specify -%s just like that, you need to enter format mode with -f.\n", badopts);
         }
         if (strlen(badopts) > 1) {
            fprintf(stderr, "Look out, you're not in format mode and you have more than one format specifier anyway: just use the -h switch for now.\n");
         }
      }

      /* Catch-all piece of logic to filter errors. */
      if ((opts.script) && (((opts.format) && (opts.human)) || ((opts.format)
                                       && (((opts.bits) && ((opts.ccount) || 
                                                            (opts.rate))) ||
                                           ((opts.ccount) && (opts.rate)))) ||
                        (opts.samples && (opts.format || opts.human)))) {
         fprintf(stderr, "Error: In script mode not more than one further mode can be specified.\n");
         print_usage(argv[0]);
         exit(1);
      }
  }

  /* Setting things up. */
  eci_init(); 
  eci_command("cs-add main"); 
  eci_command("c-add main"); 
  eci_command("ao-add null"); 

  /* Setting the format if needed. */
  if (opts.adjust) {
    if (strncmp(":", fstring, 1) == 0) { sprintf(cmd, "cs-set-audio-format %s", fstring+1); }
    else { sprintf(cmd, "cs-set-audio-format %s", fstring); }
    eci_command(cmd);
    if (strlen(eci_last_error()) != 0) {
      fprintf(stderr, "Argument to -a is badly formatted.\n");
      print_usage(argv[0]);
      exit(1);
    }
  }

  curarg = optind; 

  /* The real thing. */
  while(curarg < argc) { 
    if ((file = fopen(argv[curarg], "r")) != NULL) { 
      fclose(file); 
      sprintf(cmd, "ai-add \"%s\"", argv[curarg]); 
      eci_command(cmd); 
      eci_command("cs-connect"); 
      if (strlen(eci_last_error()) == 0) {
        sprintf(cmd, "ai-select \"%s\"", argv[curarg]);
        eci_command(cmd); 
        eci_command("ai-get-length"); 
        curfilelength = eci_last_float(); 
        if (opts.format) { 
          eci_command("ai-get-format"); 
          strcpy(fstring, eci_last_string()); 
        } 

       /* We wanted to print the length in samples so we've done nothing
        * all along; let's act now. */
        if (opts.script && opts.samples) {
            long samplecount;

            eci_command("ai-get-length-samples");
            samplecount = eci_last_long_integer();
            printf("%li", samplecount);
        }
        
        /* Here cometh the cleansing. */
        eci_command("cs-disconnect"); 
        eci_command("ai-remove"); 
        
        /* Need we humanize ourselves? */
        if (!(opts.script) || ((opts.script && opts.human))) { 
          make_human((int)(curfilelength+0.5), &min, &sec); 
        } 

        if (!(opts.script)) { printf("%s: ", argv[curarg]); } 
        if (!(opts.script) ||  
            ((opts.script) && (!(opts.format) && !(opts.human) &&
                              !(opts.samples)))) { 
          printf("%.3f", curfilelength); 
        } 
        if (!(opts.script)) { printf("s   \t("); } 
        if (!(opts.script) || ((opts.script) && (opts.human))) { 
          printf("%im%is", min, sec); 
        } 
        if (!(opts.script)) { printf(")"); } 
        if ((opts.format) && 
            !((opts.format) && ((opts.bits) || (opts.ccount) || (opts.rate)))) { 
          if (!(opts.script)) { printf("   \t"); } 
          printf("%s", fstring); 
        } 

        if ((opts.format) && (opts.script) && (opts.bits)) { 
          printf("%s", strtok(fstring+1, "_")); 
        }

        if ((opts.script) && (opts.format) && (opts.ccount)) {
          strtok(fstring, ",");
          printf("%s", strtok(NULL, ","));
        }

        if ((opts.format) && (opts.script) && (opts.rate)) {
          strtok(fstring, ",");
          strtok(NULL, ",");
          printf("%s", strtok(NULL, ","));
        }

        printf("\n"); 

        if ((opts.total) && !(opts.script)) { 
          totlength += curfilelength; 
        } 
      }
          else {
            if (opts.script) { printf("-2\n"); }
            else { printf("%s: Read error.\n", argv[curarg]); }
            status = -2;
            eci_command("ai-remove");
          }
    } 
    else { 
      if (opts.script) { printf("-1\n"); }
      else { printf("%s: fopen error.\n", argv[curarg]); }
      status = -1;
    } 
    curarg++; 
  } 

  if ((opts.total) && !(opts.script)) { 
    /* This could be made a script option as well, does anyone care? */
    make_human((int)(totlength+0.5), &min, &sec); 
    printf("Total: %.3fs \t\t(%im%is)\n", totlength, min, sec); 
  } 

  eci_command("cs-remove"); 
  eci_cleanup(); 
  exit(status); 
} 

void make_human(int length, unsigned int *min, unsigned char *sec) { 
  *min = (length/60); 
  *sec = (length % 60); 
} 

void print_help(char *name) { 
  printf("Usage: %s [-ahtsfmbcru] FILE1 [FILE2] [FILEn]\n "
       "\t-h      Prints this usage message.  (help)\n"
       "\t-a[:]bits,channels,rate     Changes the format assumed by default \n"
       "\t                            for headerless data.  (adjust)\n"
       "\t-t      Prints the summed length of all the files processed.  (total)\n"
       "\t        (Ignored if with -s) \n"
       "\t-s      Enables script mode: One info type per file per line.   (script)\n"
       "\t        (Defaults to length in secs.) \n"
       "\t-f      With -s will return the format string as info, alone it will \n"
       "\t        add it to the main display.  (format)\n"
       "\t    -b  If -s and -f are enabled with this the info printed will be \n"
       "\t        the sample's bitwidth.  (bits)\n"
       "\t    -c  If -s and -f are enabled with this the info printed will be \n"
       "\t        the channel count.  (channel count)\n"
       "\t    -r  If -s and -f are enabled with this the info printed will be \n"
       "\t        the sampling rate.  (rate)\n"
       "\t-m      Will print human computable time as in main display but in \n"
       "\t        batch fashion.  (minutes)\n"
       "\t        (Only with -s)\n"
       "\t-u      This batchmode option returns the length of specified files \n"
       "\t        in samples. (Smallest Unit)\n"
       "\t        (This information is worthless if you don't know the sampling \n"
       "\t        rate of the file.) (Only with -s)\n"
       "(Note that out of context options will be ignored.)\n\n", name);
}

void print_usage(char *name) {
    printf("Usage: %s [-ahtsfmbcru] FILE1 [FILE2] [FILEn]\n\n\t Use the -h switch for help or see the man page.\n\n", name);
}
