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
 
                  ANWIKI ADDON: DOCUMENTATION
                   structured documentations
 _____________________________________________________________________


Please visit http://www.anwiki.com/addons to get last instructions.
If you need help, read http://www.anwiki.com/support



COMPONENT DESCRIPTION
----------------------------------
This addon provides new features for creating structured documentations.


COMPONENT FEATURES
----------------------------------
This addon provides the following contentclasses:
- docbook: a documentation book
- docchapter: a chapter from a documentation book

A documentation book can have as many documentation chapters as desired.
Start by creating docchapters. Then, create a docbook and pick-up your chapters, which can be reordered at any time.
Links are automatically generated for navigating to Previous/Next chapter.
A global table of contents is generated for the whole documentation book, based on :
- chapters titles
- <h2> presents in chapters contents


INSTALL INSTRUCTIONS
----------------------------------
1. Unzip this component in your Anwiki directory.
2. Go to Anwiki config panel
3. Refresh components list
4. If necessary, configure this component from Anwiki config panel
5. Enable this component from "global > components"
