<?php

// ================= UTILITAIRE =================

/**
 * Crée une sous-partie du plateau avec le pattern placé en position ($i, $j),
 * en remplaçant les autres cases par '-'.
 */
function createBoardBis($pattern, $i, $j)
{
  $newBoard = [];
  for ($m = 0; $m < $i +  count($pattern); $m++) {
    $ligne = '';
    for ($n = 0; $n < $j +  strlen($pattern[0]); $n++) {
      if ($m < $i || $n < $j) {
        $ligne .= "-";  // On remplit les cases avant le pattern par '-'
      } else {
        $patternLigne = $m - $i;
        $patternColonne = $n - $j;

        if ($pattern[$patternLigne][$patternColonne] === ' ') {
          $ligne .= '-'; // On met un tiret pour les espaces dans le pattern
        } else {
          // Sinon on met le caractère du pattern
          $ligne .= $pattern[$patternLigne][$patternColonne];
        }
      }
    }
    $newBoard[] = $ligne;  // On ajoute la ligne construite au tableau final
  }
  return $newBoard;
}

/**
 * Vérifie si le pattern correspond au plateau à la position ($i, $j).
 * Les espaces dans le pattern sont ignorés (wildcards).
 */
function checkPattern($pattern, $board, $i, $j)
{
  $k = 0;
  $l = 0;
  while ($k < count($pattern)) {
    while ($l < strlen($pattern[$k])) {
      if ($pattern[$k][$l] === ' ') {
        // Ignore les espaces dans le pattern
      } else if ($board[$i + $k][$j + $l] !== $pattern[$k][$l]) {
        return false;  // Mismatch trouvé
      }
      $l++;
    }
    $l = 0;
    $k++;
  }
  return true;  // Le pattern correspond complètement
}

/**
 * Parcourt le plateau dans les bornes données à la recherche du pattern.
 * Retourne les coordonnées du premier match ou false si non trouvé.
 */
function boardParcour($bornes, $board, $pattern)
{
  for ($i = 0; $i <= $bornes[1]; $i++) {
    for ($j = 0; $j <= $bornes[0]; $j++) {
      if ($board[$i][$j] !== $pattern[0][0]) {
        continue; // Optimisation : première lettre du pattern doit matcher
      }
      if (checkPattern($pattern, $board, $i, $j) === true) {
        return [$i, $j];  // Pattern trouvé
      }
    }
  }
  return false; // Pattern non trouvé
}

/**
 * Calcule la dernière position possible pour commencer la recherche du pattern
 * afin qu'il rentre dans le plateau.
 */
function calculateLastStart($caracBoard, $caracPattern)
{
  $lastColone = $caracBoard[0] - $caracPattern[0];
  $lastLigne = $caracBoard[1] - $caracPattern[1];

  return [$lastColone, $lastLigne];
}

/**
 * Vérifie que le tableau (board ou pattern) est rectangulaire (toutes lignes même taille).
 */
/* function isRectangulaire($nameArray)
{
  $sizeLine = strlen($nameArray[0]);
  for ($i = 1; $i < count($nameArray); $i++) {
    if (strlen($nameArray[$i]) !== $sizeLine) {
      return false;
    }
  }
  return true;
} */

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

/**
 * Vérifie que le nombre d'arguments est correct (2 attendus).
 */
function isValidArgCount($args)
{
  return count($args) == 1;
}

/**
 * Vérifie que l'argument est une chaîne.
 */
function isStringArgument($arg)
{
  return is_string($arg);
}

// ================= PARSING =================

/**
 * Récupère les arguments passés au script (hors nom du script).
 */
function parseArguments($argv)
{
  return array_slice($argv, 1);
}

/**
 * Vérifie qu'un fichier existe, est lisible et a un nom valide.
 */
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

// ================= RESOLUTION =================

/**
 * Fonction principale qui orchestre la lecture, validation et recherche.
 */
function resolution($argv)
{
  $args = parseArguments($argv);

  if (!isValidArgCount($args)) {
    showError();
    return;
  }

  foreach ($args as $arg) {
    if (!isStringArgument($arg)) {
      showError();
      return;
    }
    if (!isValidFile($arg)) {
      showError();
      return;
    }
  }

  $board = readTheFile($args[0]);
  $pattern = readTheFile($args[1]);

  /* if (!isRectangulaire($board) || !isRectangulaire($pattern)) {
    showError();
    return;
  } */

  $caracBoard = getWidthLength($board);
  $caracPattern = getWidthLength($pattern);

  $bornes = calculateLastStart($caracBoard, $caracPattern);

  foreach ($bornes as $borne) {
    if ($borne < 0) {
      showWalou();
      return;
    }
  }

  $parcuredBoard = boardParcour($bornes, $board, $pattern);

  if ($parcuredBoard === false) {
    showWalou();
    return;
  } else {
    $positionLigne = $parcuredBoard[0];
    $positionColumn = $parcuredBoard[1];
    $areaPattern = createBoardBis($pattern, $positionLigne, $positionColumn);
    showFind($positionLigne, $positionColumn, $areaPattern);
  }
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

/**
 * Affiche la position et la zone du pattern trouvé.
 */
function showFind($positionLigne, $positionColumn, $areaPattern)
{
  echo "Trouvé !\n";
  echo "Coordonnées : " . $positionLigne . "," . $positionColumn . "\n";
  foreach ($areaPattern as $area) {
    echo $area . "\n";
  }
}

resolution($argv);
