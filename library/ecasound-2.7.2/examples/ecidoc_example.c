/*************************************************************************
 * Implementation of the following:
 * 
 * 1. Setup ECI to read audio from file, apply a 100Hz lowpass filter, and 
 *    send it to the soundcard (/dev/dsp).
 * 2. Every second, check the current position. If the stream has
 *    been running for over 15 seconds, exit immediately. Also,
 *    every second, increase the lowpass filter's cutoff frequency
 *    by 500Hz.
 * 3. Stop the stream (if not already finished) and disconnect the 
 *    chainsetup. Print chain operator status info.
 ************************************************************************/

#include <stdio.h>
#include <unistd.h>
#include <ecasoundc.h>

/* compile with: 
 *
 * gcc -o ecidoc_example ecidoc_example.c `libecasoundc-config --cflags --libs`
 */

int main(int argc, char *argv[])
{
  double cutoff_inc = 500.0;

  eci_init();
  eci_command("cs-add play_chainsetup");
  eci_command("c-add 1st_chain");
  eci_command("ai-add foo.wav");
  eci_command("ao-add /dev/dsp");
  eci_command("cop-add -efl:100");
  eci_command("cop-select 1");
  eci_command("copp-select 1");
  eci_command("cs-connect");
  eci_command("start");

  while(1) {
    double curpos, next_cutoff;

    sleep(1);
    eci_command("engine-status");
    if (strcmp(eci_last_string(), "running") != 0) break;
    eci_command("get-position");
    curpos = eci_last_float();
    if (curpos > 15.0) break;
    eci_command("copp-get");
    next_cutoff = cutoff_inc + eci_last_float();
    eci_command_float_arg("copp-set", next_cutoff);
  }
  
  eci_command("stop");
  eci_command("cs-disconnect");
  eci_command("cop-status");
  printf("Chain operator status: %s", eci_last_string());
  eci_cleanup();

  return(0);
}
