// ------------------------------------------------------------------------
// eca-chainsetup-edit.h: Chainsetup edit object
// Copyright (C) 2009 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 3
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
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

#ifndef INCLUDED_ECA_CHAINSETUP_EDIT_H
#define INCLUDED_ECA_CHAINSETUP_EDIT_H

class CHAIN;
class ECA_CHAINSETUP;

namespace ECA {
  enum Chainsetup_edit_type {
  
    edit_c_bypass = 0,
    edit_c_muting,
    edit_cop_set_param,
    edit_ctrl_set_param,
  };

  struct chainsetup_edit {

    Chainsetup_edit_type type;
    const ECA_CHAINSETUP *cs_ptr;

    /* FIXME: should a version tag be added as way to invalidate
     *        edit objects in case chainsetup is modified */

    union {
      struct {
	int chain;     /**< @see ECA_CHAINSETUP::get_chain_index() */
	bool enabled;
      } c_bypass;

      struct {
	int chain;     /**< @see ECA_CHAINSETUP::get_chain_index() */
	bool enabled;
      } c_muting;

      struct {
	int chain;     /**< @see ECA_CHAINSETUP::get_chain_index() */
	int op;        /**< @see CHAIN::set_parameter() */
	int param;     /**< @see CHAIN::set_parameter() */
	double value;  /**< @see CHAIN::set_parameter() */
      } cop_set_param;
      
      struct {
	int chain;     /**< @see ECA_CHAINSETUP::get_chain_index() */
	int op;        /**< @see CHAIN::set_controller_parameter() */
	int param;     /**< @see CHAIN::set_controller_parameter() */
	double value;  /**< @see CHAIN::set_controller_parameter() */
      } ctrl_set_param;
    } m;
  };

  typedef struct chainsetup_edit chainsetup_edit_t;
}

#endif /* INCLUDED_ECA_CHAINSETUP_EDIT_H */
