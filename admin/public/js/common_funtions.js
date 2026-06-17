function niceBytes(bytes, i) {
	var list = ["B", "KB", "MB", "GB", "TB"];

	if (typeof i == "undefined") {
		i = 0;
	}

	var temp = bytes / 1024;

	if (temp > 1024) {
		return niceBytes(temp, i + 1);
	}

	if (temp < 1) {
		return bytes.toFixed(1) + " " + list[i];
	} else {
		return temp.toFixed(1) + " " + list[i + 1];
	}
}

function niceNumbers($number, $count = 0)
{
	if ($number > 1000) {
		return niceNumbers(Math.round($number / 1000), $count + 1);
	}

	$types = ["", "K", "M", "T"];

	return $number + " " + $types[$count];
}