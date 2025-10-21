<?php

// ================= UTILITAIRE =================
function reconstructBoard($board, $solution, $fullChar)
{
  for ($m = $solution[0]; $m < $solution[0] + $solution[2]; $m++) {
    for ($n = $solution[1]; $n < $solution[1] + $solution[2]; $n++) {
      $board[$m][$n] = $fullChar;
    }
  }
  return $board;
}
function isValidCarre($board, $i, $j, $taille, $obstacleChar)
{
  $colonne = count($board[0]);
  $ligne = count($board);

  if ($i + $taille > $ligne || $j + $taille > $colonne) {
    return false;
  }

  for ($k = $i; $k < $i + $taille; $k++) {

    for ($l = $j; $l < $j + $taille; $l++) {

      if ($board[$k][$l] === $obstacleChar) {
        return false;
      }
    }
  }
  return true;
}
function findCarre($taille, $board, $obstacleChar)
{
  if ($taille == 0) {
    showWalou();
    return false;
  }
  for ($i = 0; $i < count($board); $i++) {
    for ($j = 0; $j < count($board[$i]); $j++) {
      if (isValidCarre($board, $i, $j, $taille, $obstacleChar)) {
        return [$i, $j, $taille];
      }
    }
    # code...
  }
  $result = findCarre($taille - 1, $board, $obstacleChar);
  return $result;
}

function isValidSizeCol($board, $instruction)
{
  $indice = intval($instruction[0]);
  $height = count($board);
  if ($height !== $indice) {
    return false;
  }

  return true;
}

// La function ci dessou verifie qu'on ai le meme nombre de colonne dans toute les lignes
function isValidSizeRow($board)

{
  $referenceWidth = count($board[0]);
  foreach ($board as $row) {
    if (count($row) !== $referenceWidth) {
      return false;
    }
  }
  return true;
}
function isValidInstruction($instruction)
{

  $reverse = array_reverse($instruction);
  for ($i = 0; $i < count($reverse) - 1; $i++) {
    if (!ctype_alpha($reverse[$i]) && !ctype_punct($reverse[$i])) {
      return false;
    }
  }

  for ($i = 0; $i < count($instruction) - 3; $i++) {
    if (!ctype_digit($instruction[$i])) {
      return false;
    }
  }

  return true;
}
function readTheFile($fileName)
{
  $fileLines = [];

  // On ouvre le fichier en lecture seule
  $handle = fopen($fileName, "r");

  // Lecture ligne par ligne jusqu'à la fin du fichier
  while (!feof($handle)) {
    $fileLines[] = str_split(rtrim(fgets($handle), "\r\n"));
  }

  // Fermeture du fichier
  fclose($handle);

  // On renvoie le tableau des lignes
  return $fileLines;
}
function getDimension($nameArray)
{
  $widthArray = count($nameArray[0]);
  $heightArray = count($nameArray);

  return [$widthArray, $heightArray];
}


// ================= GESTION DES ERREURS =================
function isValidArgCount($argument)
{
  return count($argument) == 1;
}


function isStringArgument($argument)
{
  return is_string($argument);
}

function isValidFile($fileName)
{
  if (!is_string($fileName)) {
    return false;
  }
  if (!file_exists($fileName)) {
    return false;
  }
  if (!is_readable($fileName)) {
    return false;
  }
  return true;
}

function getValidBoard($board, $instruction)
{
  if (!isValidInstruction($instruction)) {
    showError();
    exit;
  }
  if (!isValidSizeRow($board)) {
    showError();
    exit;
  }
  if (!isValidSizeCol($board, $instruction)) {
    showError();
    exit;
  }

  return $board;
}

// ================= PARSING =================
function parseArguments($argv)
{
  return array_slice($argv, 1);
}

// ================= RESOLUTION =================
function resolution($argv)
{
  $arg = parseArguments($argv);

  if (!isValidArgCount($arg)) {
    showError();
    return;
  }

  if (!isStringArgument($arg[0])) {
    showError();
    return;
  }

  if (!isValidFile($arg[0])) {
    showError();
    return;
  }

  $board = readTheFile($arg[0]);
  $instruction = array_shift($board);
  $boardValidated = getValidBoard($board, $instruction);

  $dimension = getDimension($boardValidated);
  $obstacle = $instruction[count($instruction) - 2];

  $maxSize = min($dimension[0], $dimension[1]);

  $solution = findCarre($maxSize, $boardValidated, $obstacle);
  $fullChar = $instruction[count($instruction) - 1];
  $finalBoard = reconstructBoard($boardValidated, $solution, $fullChar);
  showSucces($finalBoard);
}

// ================= AFFICHAGE =================
function showError()
{
  echo "error\n";
}
function showWalou()
{
  echo "Introuvable\n";
}
function showSucces($board)
{
  foreach ($board as $row) {
    echo implode('', $row) . "\n";
  }
}




resolution($argv);
