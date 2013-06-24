/**
 * Anwiki is a multilingual content management system <http://www.anwiki.com>
 * Copyright (C) 2007-2009 Antoine Walter <http://www.anw.fr>
 * 
 * Anwiki is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 * 
 * Anwiki is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Anwiki.  If not, see <http://www.gnu.org/licenses/>.
 */

 _____________________________________________________________________
 
                    ANWIKI ADDON: SYNCTRFILES
          Import, synchronize and export translation files
 _____________________________________________________________________


Please visit http://www.anwiki.com/addons to get last instructions.
If you need help, read http://www.anwiki.com/support



COMPONENT DESCRIPTION
----------------------------------
This addon enables developers to import, synchronize and export various translation files (PO, PHP).
PHP programming skills are required to set-up your own translation files handlers.


COMPONENT FEATURES
----------------------------------
This addon provides a new action available from the "manage" area.
From this page, translation files can be synchronized (imported) or exported.

/!\ This action needs to be overriden for declaring your own translation files handlers.
You have to edit the file _override/actions/action_synctrfiles/action_synctrfiles.php
to tell the system where to import/export your translation files.


INSTALL INSTRUCTIONS
----------------------------------
1. Unzip this component in your Anwiki directory.
2. Go to Anwiki config panel
3. Refresh components list
4. If necessary, configure this component from Anwiki config panel
5. Enable this component from "global > components"
