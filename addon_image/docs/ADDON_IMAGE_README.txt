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
 
                        ANWIKI ADDON: IMAGE
             editing & translating images stored in base64
 _____________________________________________________________________


Please visit http://www.anwiki.com/addons to get last instructions.
If you need help, read http://www.anwiki.com/support



COMPONENT DESCRIPTION
----------------------------------
This addon enables you to edit and translates images from Anwiki (in PNG base64).
Related discussion: http://bugs.anwiki.com/index.php?do=details&task_id=73

COMPONENT FEATURES
----------------------------------
This addon provides:
- the plugin: image
- the contentclass: image

Example of use:
- Create an new content of type "Image", in example "en/snoopy" then copy/paste PNG base64 code of your image and save
  This content can be translated in as many languages as needed.
- Then, insert your image in any HTML content from Anwiki as following: <img src="en/snoopy"/>
- That's it, your image will appear and be translated according to your main content language.


INSTALL INSTRUCTIONS
----------------------------------
1. Unzip this component in your Anwiki directory.
2. Go to Anwiki config panel
3. Refresh components list
4. If necessary, configure this component from Anwiki config panel
5. Enable this component from "global > components"


AUTHORS
----------------------------------
- Wladimir Palant (trev)
- Antoine Walter (anw)
