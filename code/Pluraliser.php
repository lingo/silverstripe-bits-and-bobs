<?php

/**
 * A very simple decorator which adds a method to DataObjectSet to allow easy 
 * descriptions of result counts in templates.
 *
 * Installation:
 *	
 *	1. Put this somewhere in your mysite/code folder tree.
 *	2. Enter the following line in somewhere in your mysite/_config.php
 *
 *		Object::add_extension('DataObjectSet', 'Pluraliser');
 *
 *	3. Run: sake dev/build or visit  http://YOUR_SITE_DOMAIN_NAME_HERE/dev/build in your browser
 *
 * Usage:
 *
 * 		<% if Items %>
 *			<span>$Items.Count $Items.Plural(item) were found.</span>
 * 		<% end_if %>
 *
 * 	OR (this is contrived, as the plural for category would be generated).
 * 		<% if Categories %>
 *			<span>$Categories.Count $Categories.Plural(category,categories) were found.</span>
 * 		<% end_if %>
 *
 */
class Pluraliser extends DataObjectDecorator {


	/**
	 * Return the singular or plural of the provided word(s), 
	 * depending on the number of items in this DataObjectSet.
	 *
	 * @param string $word	-	The singular version of the word, this will be pluralised if needed.
	 * @param string $wordPl -	Optional plural to use. If not provided, a plural will be guessed.
	 * @return string - Singular or plural according to number of items in the set.
	 */
	public function Plural($word, $wordPl = null) {
		$usePlural = ($this->owner->Count() != 1);
		if (!$wordPl) {
			// Try to guess a plural if the caller didn't provide one.
			$last = substr($word, -1, 1);
			if ($last == 'y') {
				$wordPl = substr($word, 0, -1) . 'ies';
			} elseif ($last == 'f') {
				$wordPl = substr($word, 0, -1) . 'ves';
			} else {
				$wordPl = $word . 's';
			}
		}
		return $usePlural ? $wordPl : $word;
	}
}

