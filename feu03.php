<?php

// ================= UTILITAIRE =================
function getValue()
{
  $possibility = [];
  for ($m = 1; $m <= 9; $m++) {
    $possibility[] = $m;
  }
  return $possibility;
}
function isInRow($board, $i, $value)
{
  $ligne = $board[$i];
  for ($k = 0; $k < strlen($ligne); $k++) {
    if ($ligne[$k] == $value) {
      return true;
    }
  }
  return false;
}

function isInCol($board, $j, $value)
{
  for ($l = 0; $l < count($board); $l++) {
    if ($board[$l][$j] == $value) {
      return true;
    }
  }
  return false;
}

function isInCarre($board, $i, $j, $value)
{
  $row = $i - 1;
  $col = $j - 1;
  for ($row; $row <= $i + 1; $row++) {
    if ($row || $row >= count($board)) {
      continue;
    }

    for ($col; $col <= $j + 1; $col++) {


      if ($col < 0 || $col >= strlen(($board[0]))) {
        continue;
      }

      if ($board[$row][$col] === $value) {
        return true;
      }
    }
  }
  return false;
}


function isEmpty($board, $i, $j)
{
  if ($board[$i][$j] == ".") {
    return true;
  }
  if ($board[$i][$j] == " ") {
    return true;
  }
  return false;
}

function boardParcourDFS($bornes, $board,)
{
  for ($i = 0; $i < $bornes[1]; $i++) {
    for ($j = 0; $j < $bornes[0]; $j++) {
      if (!isEmpty($board, $i, $j)) {
        continue;
      }
      $values = getValue();
      foreach ($values as $value) {
        if (!isInRow($board, $i, $value)) {
          if (!isInCol($board, $j, $value)) {
            if (!isInCarre($board, $i, $j, $value)) {
              $newBoard = $board;
              $newBoard[$i][$j] = $value;
              $result = boardParcourDFS($bornes, $newBoard);

              if ($result !== false) {
                return $result;
              }
              $newBoard[$i][$j] = '.';
            }
          }
        }
      }
      return false;
    }
  }
  return $board;
}


/**
 * Récupère la largeur (nombre de colonnes) et la hauteur (nombre de lignes) du tableau.
 */
function getWidthLength($nameArray)
{
  $widthLine = strlen($nameArray[0]);
  $lengthLine = count($nameArray);

  return [$widthLine, $lengthLine];
}
/**
 * Lit un fichier et renvoie un tableau des lignes sans retour à la ligne.
 */
function readTheFile($fileName)
{
  $fileLines = [];

  // On ouvre le fichier en lecture seule
  $handle = fopen($fileName, "r");

  // Lecture ligne par ligne jusqu'à la fin du fichier
  while (!feof($handle)) {
    $fileLines[] = rtrim(fgets($handle), "\r\n");
  }

  // Fermeture du fichier
  fclose($handle);

  // On renvoie le tableau des lignes
  return $fileLines;
}


// ================= GESTION DES ERREURS =================
function isValidArgCount($args)
{
  return count($args) == 1;
}


function isStringArgument($arg)
{
  return is_string($arg);
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

  $file = readTheFile($arg[0]);

  $dimensionBoard = getWidthLength($file);
  $succes = boardParcourDFS($dimensionBoard, $file);
  if ($succes == false) {
    showError();
    return;
  }
  showSucces($succes);
}

// ================= AFFICHAGE =================
function showError()
{
  echo "error\n";
}
function showSucces($board)
{
  foreach ($board as $row) {
    echo $row . "\n";
  }
}

resolution($argv);
