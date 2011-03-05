/** 
 * @file pyecasound.c Python interface to the ecasound control interface
 */

// ------------------------------------------------------------------------
// pyecasound.cpp: Python interface to the ecasound control interface
// Copyright (C) 2000-2002,2008 Kai Vehmanen
//
// This library is free software; you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public
// License as published by the Free Software Foundation; either
// version 2.1 of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
// ------------------------------------------------------------------------

#include <Python.h>
#include <ecasoundc.h>
#include "pyecasound.h"

typedef struct {
  PyObject_HEAD
  eci_handle_t eci;
} pyeca_control_t;

//  staticforward PyTypeObject pyeca_control_type;

static void pyeca_control_del(PyObject *self, PyObject *args);
static PyObject* pyeca_getattr(PyObject *self, char *name);

// ********************************************************************/

static PyObject * pyeca_command(PyObject* self, PyObject *args)
{
  char *str;
  pyeca_control_t *selfp;

  if (!PyArg_ParseTuple(args, "s", &str)) return NULL;
  selfp = (pyeca_control_t*) self;
  eci_command_r(selfp->eci, str);

  return Py_BuildValue("");
}

static PyObject * pyeca_command_float_arg(PyObject* self, PyObject *args)
{
  char *str;
  double v;
  pyeca_control_t *selfp;

  if (!PyArg_ParseTuple(args, "sd", &str, &v)) return NULL;
  selfp = (pyeca_control_t*) self;

  eci_command_float_arg_r(selfp->eci, str, v);

  return Py_BuildValue("");
}

static PyObject * pyeca_last_string_list(PyObject* self, PyObject *args)
{
  pyeca_control_t *selfp = (pyeca_control_t*) self;
  int count = eci_last_string_list_count_r(selfp->eci);
  int n;

  PyObject *list = Py_BuildValue("[]");
  for(n = 0; n < count; n++) {
    PyList_Append(list, Py_BuildValue("s", eci_last_string_list_item_r(selfp->eci, n)));
  }

  return(list);
}

static PyObject * pyeca_last_string(PyObject* self, PyObject *args)
{
  pyeca_control_t *selfp = (pyeca_control_t*) self;
  return Py_BuildValue("s", eci_last_string_r(selfp->eci));
}

static PyObject * pyeca_last_float(PyObject* self, PyObject *args)
{
  pyeca_control_t *selfp = (pyeca_control_t*) self;
  return Py_BuildValue("d", eci_last_float_r(selfp->eci));
}

static PyObject * pyeca_last_integer(PyObject* self, PyObject *args)
{
  pyeca_control_t *selfp = (pyeca_control_t*) self;
  return Py_BuildValue("i", eci_last_integer_r(selfp->eci));
}

static PyObject * pyeca_last_long_integer(PyObject* self, PyObject *args)
{
  pyeca_control_t *selfp = (pyeca_control_t*) self;
  return Py_BuildValue("l", eci_last_long_integer_r(selfp->eci));
}

static PyObject * pyeca_last_error(PyObject* self, PyObject *args)
{
  pyeca_control_t *selfp = (pyeca_control_t*) self;
  return Py_BuildValue("s", eci_last_error_r(selfp->eci));
}

static PyObject * pyeca_error(PyObject* self, PyObject *args)
{
  pyeca_control_t *selfp = (pyeca_control_t*) self;
  int i = eci_error_r(selfp->eci);
  return Py_BuildValue("i", i);
}

static PyObject * pyeca_last_type(PyObject* self, PyObject *args)
{
  pyeca_control_t *selfp = (pyeca_control_t*) self;
  return Py_BuildValue("s", eci_last_type_r(selfp->eci));
}

static PyObject * pyeca_events_available(PyObject* self, PyObject *args)
{
  return Py_BuildValue("i", 0);
}

static PyObject * pyeca_next_event(PyObject* self, PyObject *args)
{
  return Py_BuildValue("");
}

static PyObject * pyeca_current_event(PyObject* self, PyObject *args)
{
  return Py_BuildValue("");
}

static struct PyMethodDef pyeca_control_methods[] = {
  { "command",               pyeca_command,                METH_VARARGS},
  { "command_float_arg",     pyeca_command_float_arg,      METH_VARARGS},
  { "last_string_list",      pyeca_last_string_list,       METH_VARARGS},
  { "last_string",           pyeca_last_string,            METH_VARARGS},
  { "last_float",            pyeca_last_float,             METH_VARARGS},
  { "last_integer",          pyeca_last_integer,           METH_VARARGS},
  { "last_long_integer",     pyeca_last_long_integer,      METH_VARARGS},
  { "last_error",            pyeca_last_error,             METH_VARARGS},
  { "last_type",             pyeca_last_type,              METH_VARARGS},
  { "error",                 pyeca_error,                  METH_VARARGS},
  { "events_available",      pyeca_events_available,       METH_VARARGS},
  { "next_event",            pyeca_next_event,             METH_VARARGS},
  { "current_event",         pyeca_current_event,          METH_VARARGS},
  { NULL,                    NULL }
};

// ********************************************************************/

static PyTypeObject pyeca_control_type = {
	PyObject_HEAD_INIT(&PyType_Type)
	0,
	"ECA_CONTROL_INTERFACE",
	sizeof (pyeca_control_t),
	0,
	(destructor)  pyeca_control_del,
	0,
	(getattrfunc) pyeca_getattr,
	0,
	0,
	0,
	0,
	0,
	0,
	0,
	0,
	0,
};

// ********************************************************************/

static PyObject *pyeca_control_new(PyObject *self, PyObject *args)
{
  pyeca_control_t *selfp = (pyeca_control_t*) PyObject_New(pyeca_control_t, &pyeca_control_type);

  selfp->eci = eci_init_r();
  self = (PyObject *) selfp;

  return(self);
}

static PyObject* pyeca_getattr(PyObject *self, char *name)
{
  return Py_FindMethod(pyeca_control_methods, (PyObject*) self, name);
}

static void pyeca_control_del(PyObject *self, PyObject *args)
{
  pyeca_control_t *selfp = (pyeca_control_t*) self;

  eci_cleanup_r(selfp->eci);

  PyObject_Del(self);
}

static PyMethodDef pyecamethods[] = {
  {"ECA_CONTROL_INTERFACE",    pyeca_control_new,    METH_VARARGS },
  {NULL,		       NULL }
};

void initpyecasound(void)
{
  Py_InitModule("pyecasound", pyecamethods);
}
