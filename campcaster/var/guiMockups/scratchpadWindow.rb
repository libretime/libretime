#!/usr/bin/env ruby

#-------------------------------------------------------------------------------
#   Copyright (c) 2007 Media Development Loan Fund
#
#   This file is part of the Campcaster project.
#   http://campcaster.campware.org/
#   To report bugs, send an e-mail to bugs@campware.org
#
#   Campcaster is free software; you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation; either version 2 of the License, or
#   (at your option) any later version.
#
#   Campcaster is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#
#   You should have received a copy of the GNU General Public License
#   along with Campcaster; if not, write to the Free Software
#   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#   Author   : $Author$
#   Version  : $Revision$
#   Location : $URL$
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#   Mockup of the Scratchpad window.
#
#   glade file: scratchpadWindow.glade
#-------------------------------------------------------------------------------

require 'libglade2'

class ScratchpadWindow
    public
        def initialize(path)
            @glade = GladeXML.new(path) {
                |handler| method(handler)
            }
            
            @mainWindow = @glade["window1"]
            @mainWindow.signal_connect("hide") {
                Gtk.main_quit
            }

            playButton = @glade["button1"]
            playButtonImage = Gtk::Image.new(Gtk::Stock::MEDIA_PLAY,
                                             Gtk::IconSize::BUTTON)
            playButton.image = playButtonImage
            
            stopButton = @glade["button2"]
            stopButtonImage = Gtk::Image.new(Gtk::Stock::MEDIA_STOP,
                                             Gtk::IconSize::BUTTON)
            stopButton.image = stopButtonImage

            @listStore = Gtk::ListStore.new(String)
            addrow("Song One")
            addrow("Song Two")
            addrow("Song Three")
            
            treeView = @glade["treeview1"]
            treeView.model = @listStore
            
            cwd = File.dirname(__FILE__)
            audioClipIcon = Gdk::Pixbuf.new(cwd + "/audioClipIcon.png")
            cellRenderer0 = Gtk::CellRendererPixbuf.new
            cellRenderer0.pixbuf = audioClipIcon
            treeViewColumn0 = Gtk::TreeViewColumn.new("Type",
                                                      cellRenderer0)
            treeView.append_column(treeViewColumn0)
            
            cellRenderer1 = Gtk::CellRendererText.new
            treeViewColumn1 = Gtk::TreeViewColumn.new("Title",
                                                      cellRenderer1,
                                                      :text => 0)
            treeView.append_column(treeViewColumn1)
            
            treeView.selection.mode = Gtk::SELECTION_MULTIPLE
        end

        def addrow(contents)
            iter = @listStore.append
            iter[0] = contents
        end

        def run
            @mainWindow.show_all
            Gtk.main
        end
end

cwd = File.dirname(__FILE__)
Gtk::RC.default_files = [cwd + "/themes/MacOS-X/gtk-2.0/gtkrc"]
Gtk.init
scratchpadWindow = ScratchpadWindow.new(cwd + "/scratchpadWindow.glade")
scratchpadWindow.run

