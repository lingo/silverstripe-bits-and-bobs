# Bits and bobs #

**Author**: Luke Hudson <lukeletters@gmail.com>

Here are some classes that aren't worth making into full modules.

Each class should be documented well at the top of the file, with usage and installation instructions.

Here's a quick list of what's inside the pack.

## Contents ##


### Pluraliser ###

Adds the function Plural(word) to DataObjectSet, making it simple to write your template like this:

    $Items.Count $Items.Plural(item) found
 
 and not fiddling around with ifs like this (if it even works):
 
    $Items.Count <% if Items.Count = 1 %>item<% else %>items<% end_if %>
 
in your template to correctly show singular or plural depending on number of items found.

### HTML5 Boilerplate ###

Nothing more than a SilverStripe-d custom version of HTML from http://html5boilerplate.com/

### ImageRotatorPage ###

A page type with associated templates and javascript, which allows you to have a page where images 
from a selected source folder are slowly cycled in different panes.

