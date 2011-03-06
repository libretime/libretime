#ifndef INCLUDED_ECASOUNDC_H
#define INCLUDED_ECASOUNDC_H

/** ------------------------------------------------------------------------
 * ecasoundc.h: Standalone C implementation of the 
 *              ecasound control interface
 * Copyright (C) 2000-2002 Kai Vehmanen
 * Copyright (C) 2001 Aymeric Jeanneau
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * -------------------------------------------------------------------------
 */

#ifdef __cplusplus
extern "C" {
#endif

/* ---------------------------------------------------------------------
 * Reference on object
 */
typedef void * eci_handle_t;

/* ---------------------------------------------------------------------
 * Constructing and destructing                                       
 */

void eci_init(void);
eci_handle_t eci_init_r(void);

int eci_ready(void);
int eci_ready_r(eci_handle_t p);

void eci_cleanup(void);
void eci_cleanup_r(eci_handle_t p);
 
/* ---------------------------------------------------------------------
 * Issuing EIAM commands 
 */

void eci_command(const char* cmd);
void eci_command_r(eci_handle_t p, const char* cmd);

void eci_command_float_arg(const char*, double arg);
void eci_command_float_arg_r(eci_handle_t p, const char*, double arg);

/* ---------------------------------------------------------------------
 * Getting return values 
 */

int eci_last_string_list_count(void);
int eci_last_string_list_count_r(eci_handle_t p);

const char* eci_last_string_list_item(int n);
const char* eci_last_string_list_item_r(eci_handle_t p, int n);

const char* eci_last_string(void);
const char* eci_last_string_r(eci_handle_t p);

double eci_last_float(void);
double eci_last_float_r(eci_handle_t p);

int eci_last_integer(void);
int eci_last_integer_r(eci_handle_t p);

long int eci_last_long_integer(void);
long int eci_last_long_integer_r(eci_handle_t p);

const char* eci_last_error(void);
const char* eci_last_error_r(eci_handle_t p);

const char* eci_last_type(void);
const char* eci_last_type_r(eci_handle_t p);

int eci_error(void);
int eci_error_r(eci_handle_t p);
 
/* --------------------------------------------------------------------- 
 * Events 
 */

int eci_events_available(void);
int eci_events_available_r(eci_handle_t p);

void eci_next_event(void);
void eci_next_event_r(eci_handle_t p);

const char* eci_current_event(void);
const char* eci_current_event_r(eci_handle_t p);

#ifdef __cplusplus
}
#endif

#endif
