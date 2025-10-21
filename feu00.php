<?php
// Utilitaire


function buildRectangle($width, $height)
{
  $lines = [];
  for ($i = 0; $i <= $height - 1; $i++) {
    if ($i == 0 || $i == $height - 1) {
      $lines[] = buildHorizontalLine($width);
    } else {
      $lines[] = buildMiddleLine($width);
    }
  }
  return $lines;
}
function buildHorizontalLine($width)
{

  $horizontalLine = "";


  for ($i = 0; $i <= $width - 1; $i++) {
    if ($i == 0 || $i == $width - 1) {
      $horizontalLine .= "o";
    } else {
      $horizontalLine .= "-";
    }
  }
  return $horizontalLine;
};
function buildMiddleLine($width)
{
  $verticalLine = "";
  for ($i = 0; $i <= $width - 1; $i++) {
    if ($i == 0 || $i == $width - 1) {
      $verticalLine .= "|";
    } else {
      $verticalLine .= " ";
    }
  }
  return $verticalLine;
};
function showError()
{
  echo "error\n";
}

// Error Handling

function isValidCount($arguments)
{
  if (count($arguments) !== 2) {
    return false;
  } else {

    return true;
  }
}
function isNumber($arguments)
{
  if (!ctype_digit($arguments)) {
    return false;
  } else {
    return true;
  }
}
function isEmpty($arguments)
{
  if (trim($arguments) === '') {
    return false;
  } else {
    return true;
  }
}
function isPositive($arguments)
{
  if ($arguments < 1) {
    return false;
  } else {
    return true;
  }
}


// Parsing

function parseArguments($argv)
{
  $arguments = array_slice($argv, 1);
  return $arguments;
}

// Résolution

function resolution($argv)
{
  $arguments = parseArguments($argv);
  if (!isValidCount($arguments)) {
    showError();
    return;
  }
  $numbers = [];
  foreach ($arguments as $arg) {
    if (!isNumber($arg) || !isPositive($arg) || !isEmpty($arg)) {
      showError();
      return;
    } else {
      $numbers[] = (int)$arg;
    }
  }
  $width = $numbers[0];
  $height = $numbers[1];


  $rectangle = buildRectangle($width, $height);
  displayRectangle($rectangle);
}

// Affichage

function displayRectangle($rectangle)
{
  foreach ($rectangle as $rec) {
    echo $rec . "\n";
  }
}

resolution($argv);
