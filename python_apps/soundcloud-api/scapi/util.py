##    SouncCloudAPI implements a Python wrapper around the SoundCloud RESTful
##    API
##
##    Copyright (C) 2008  Diez B. Roggisch
##    Contact mailto:deets@soundcloud.com
##
##    This library is free software; you can redistribute it and/or
##    modify it under the terms of the GNU Lesser General Public
##    License as published by the Free Software Foundation; either
##    version 2.1 of the License, or (at your option) any later version.
##
##    This library is distributed in the hope that it will be useful,
##    but WITHOUT ANY WARRANTY; without even the implied warranty of
##    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
##    Lesser General Public License for more details.
##
##    You should have received a copy of the GNU Lesser General Public
##    License along with this library; if not, write to the Free Software
##    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

import urllib

def escape(s):
    # escape '/' too
    return urllib.quote(s, safe='')






class MultiDict(dict):


    def add(self, key, new_value):
        if key in self:
            value = self[key]
            if not isinstance(value, list):
                value = [value]
                self[key] = value
            value.append(new_value)
        else:
            self[key] = new_value


    def iteritemslist(self):
        for key, value in self.iteritems():
            if not isinstance(value, list):
                value = [value]
            yield key, value


    
