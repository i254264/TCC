<?php

function parseBib($bibFile)
{
    $content = file_get_contents($bibFile);
    if ($content === false) {
        die("Unable to open file!");
    }

    return bibLinesToArray(preg_split('/\r\n|\r|\n/', $content));
}

function readBib($bib)
{
    $content = file_get_contents($bib);
    if ($content === false) {
        die("Unable to open file!");
    }

    return preg_split('/\r\n|\r|\n/', $content);
}

function bibLinesToArray($bibLines)
{
    $content = implode("\n", $bibLines);
    $content = preg_replace('/\r\n?/', "\n", $content);

    $entries = array();
    $length = strlen($content);
    $offset = 0;

    while ($offset < $length) {
        $start = strpos($content, "@", $offset);
        if ($start === false) {
            break;
        }

        $typeStart = $start + 1;
        while ($typeStart < $length && ctype_space($content[$typeStart])) {
            $typeStart++;
        }

        $typeEnd = $typeStart;
        while ($typeEnd < $length && preg_match('/[A-Za-z]/', $content[$typeEnd])) {
            $typeEnd++;
        }

        if ($typeEnd === $typeStart) {
            $offset = $start + 1;
            continue;
        }

        $type = strtoupper(substr($content, $typeStart, $typeEnd - $typeStart));

        $openIndex = $typeEnd;
        while ($openIndex < $length && $content[$openIndex] !== '{' && $content[$openIndex] !== '(') {
            $openIndex++;
        }

        if ($openIndex >= $length) {
            break;
        }

        $openChar = $content[$openIndex];
        $balance = 0;
        $closeIndex = null;

        for ($i = $openIndex; $i < $length; $i++) {
            $char = $content[$i];
            if ($char === '{' || $char === '(') {
                if ($char === $openChar) {
                    $balance++;
                }
            } elseif ($char === '}' || $char === ')') {
                if ($char === ($openChar === '{' ? '}' : ')')) {
                    $balance--;
                    if ($balance === 0) {
                        $closeIndex = $i;
                        break;
                    }
                }
            }
        }

        if ($closeIndex === null) {
            break;
        }

        $body = substr($content, $openIndex + 1, $closeIndex - $openIndex - 1);
        $entry = parseBibEntry($body, $type);
        if (!empty($entry)) {
            $entries[] = $entry;
        }

        $offset = $closeIndex + 1;
    }

    return $entries;
}

function parseBibEntry($body, $type)
{
    $record = array();
    $record['type'] = strtolower($type);

    $body = trim($body);
    $body = preg_replace('/^\s*[^\s=,]+\s*,\s*/', '', $body, 1);
    $body = trim($body);
    $length = strlen($body);
    $index = 0;

    while ($index < $length) {
        while ($index < $length && preg_match('/[\s,]/', $body[$index])) {
            $index++;
        }

        if ($index >= $length) {
            break;
        }

        $keyStart = $index;
        while ($index < $length && !preg_match('/[\s=]/', $body[$index])) {
            $index++;
        }

        $key = trim(substr($body, $keyStart, $index - $keyStart));
        if ($key === '') {
            $index++;
            continue;
        }

        while ($index < $length && ctype_space($body[$index])) {
            $index++;
        }

        if ($index < $length && $body[$index] === '=') {
            $index++;
            while ($index < $length && ctype_space($body[$index])) {
                $index++;
            }
        }

        if ($index >= $length) {
            break;
        }

        $value = '';
        if ($body[$index] === '{') {
            $depth = 0;
            $j = $index;
            while ($j < $length) {
                if ($body[$j] === '{') {
                    $depth++;
                } elseif ($body[$j] === '}') {
                    $depth--;
                    if ($depth === 0) {
                        $value = substr($body, $index + 1, $j - $index - 1);
                        $index = $j + 1;
                        break;
                    }
                }
                $j++;
            }
        } elseif ($body[$index] === '"') {
            $index++;
            $valueStart = $index;
            while ($index < $length) {
                if ($body[$index] === '\\') {
                    $index += 2;
                    continue;
                }
                if ($body[$index] === '"') {
                    $value = substr($body, $valueStart, $index - $valueStart);
                    $index++;
                    break;
                }
                $index++;
            }
        } else {
            $valueStart = $index;
            while ($index < $length && $body[$index] !== ',') {
                $index++;
            }
            $value = trim(substr($body, $valueStart, $index - $valueStart));
        }

        $value = clean($value);
        if ($value !== '') {
            $record[strtolower($key)] = $value;
        }

        while ($index < $length && $body[$index] !== ',') {
            $index++;
        }

        if ($index < $length && $body[$index] === ',') {
            $index++;
        }
    }

    return $record;
}

function get_string_between($string, $start, $end)
{
    $string = " " . $string;
    $ini = strpos($string, $start);
    if ($ini === false || $ini === 0) {
        return "";
    }
    $ini += strlen($start);
    $len = strpos($string, $end, $ini);
    if ($len === false) {
        return "";
    }
    return substr($string, $ini, $len - $ini);
}

function clean($string)
{
    $string = trim((string) $string);
    $string = str_replace("\r", " ", $string);
    $string = str_replace("\n", " ", $string);
    $string = preg_replace('/\\\s*\{/', '', $string);
    $string = preg_replace('/\\\s*\}/', '', $string);
    $string = preg_replace('/\\\\[A-Za-z]+/', '', $string);
    $string = preg_replace('/\\\./', '', $string);
    $string = str_replace(array('{', '}', '"', '$', "'", '\\'), '', $string);
    $string = preg_replace('/\s+/', ' ', $string);
    return trim($string);
}

function alfaNum($string)
{
    $string = preg_replace('/[^a-zA-Z0-9]+/', '', $string);
    return $string;
}
?>
