<?php
echo '<h1>Hi man</h1>'

/* ------- copy de function image de denis ------ */

/**
 * Clean a string to remove special characters and replace space with a delimiter
 * Ex: Bob l'éponge => bob+l+eponge
 * @param  string $str
 * @param  array $replace
 * @param  char $delimiter
 */
function cleanString($str, $replace=array(), $delimiter='+') {
	if( !empty($replace) ) {
		$str = str_replace((array)$replace, ' ', $str);
	}
	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	$clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
	return $clean;
}

/**
 * Make a search from the Google Image API,
 * Browse the results,
 * Exclude "not found", "forbidden", "unavailable" status,
 * Return the path of the found image.
 * If not found, return an empty string.
 * If $thumb = true return the thumbnail image width, ortherwise return the full image width.
 * @param  string $search
 * @param  bool $thumb
 */
function getGoogleImg($search, $thumb = false) {

	// Clean search string to remove special chars, and replace spaces with +
	$clean_str = cleanString($search, array(), '+');

	// If $thumb = true, look for the thumbnail url, otherwise look for the full image url
	$target = ($thumb ? 'tbUrl' : 'url');

	// Construct the Google Image API query
	$query = 'https://ajax.googleapis.com/ajax/services/search/images?v=1.0&q='.urlencode($clean_str);

	// Get the result from query, returns a JSON object
	$json = file_get_contents($query);

	// Converts the JSON object in PHP array
	$results = json_decode($json, true);

	// If there are results from the query
	if (!empty($results["responseData"]["results"])) {

		// Browse each result from response and set it in $result
		foreach ($results["responseData"]["results"] as $result) {

			// Retrieve the HTTP headers
			$file_headers = @get_headers($result[$target]);

			// If HTTP headers don't contain status 403 (forbidden), 404 (not found) or 503 (unavailable)
			if(strpos($file_headers[0], '403') === false &&
			   strpos($file_headers[0], '404') === false &&
			   strpos($file_headers[0], '503') === false) {

			   	// Return the absolute image path (http://...) from result with $target as key
			   	return $result[$target];
			}
		}
	}

	// No image found, return an empty string
	return '';
}

?>
<form action='clairaton.php' method='POST'>
	<input type='text' name='search img'>
	<input type='submit' value='ok'>
</form>

