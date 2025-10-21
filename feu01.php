<?php

// Utilitaire


/* function peek($stack)
{
  if (count($stack) == 0) {
    return null;
  }
  return $stack[count($stack) - 1];
}
function pop($stack)
{
  $element = array_pop($stack);
  return [$element, $stack];
}
function push($stack, $value)
{
  $stack[] = ($value);
  return $stack;
}

function isEmpty($stack)
{
  if (count($stack) != 0) {
    return false;
  }
  return true;
} */



/**
 * Vide entièrement la pile d'opérateurs en calculant les résultats restants.
 * Retourne le tableau des nombres et celui des opérateurs.
 */
function flushOperatorsStack($numbersStack, $operatorsStack)
{
  while (count($operatorsStack) !== 0) {
    $operator = array_pop($operatorsStack);
    $right = array_pop($numbersStack);
    $left = array_pop($numbersStack);
    $result = calculate($operator, $left, $right);
    $numbersStack[] = $result;
  }
  return [$numbersStack, $operatorsStack];
}

/**
 * Traite la fermeture d'une parenthèse.
 * Dépile les opérateurs jusqu'à la parenthèse ouvrante et calcule à chaque étape.
 */
function processClosingParenthesis($numbersStack, $operatorsStack)
{
  while (count($operatorsStack) !== 0 && $operatorsStack[count($operatorsStack) - 1] !== "(") {
    $operator = array_pop($operatorsStack);
    $right = array_pop($numbersStack);
    $left = array_pop($numbersStack);
    $result = calculate($operator, $left, $right);
    $numbersStack[] = $result;
  }
  // Enlève la parenthèse ouvrante de la pile d'opérateurs
  array_pop($operatorsStack);
  return [$numbersStack, $operatorsStack];
}

/**
 * Gère la priorité d'un nouvel opérateur :
 * Dépile les opérateurs de priorité supérieure ou égale avant d'empiler le nouveau.
 */
function processOperator($numbersStack, $operatorsStack, $currentOperator)
{
  while (
    count($operatorsStack) > 0 &&
    $operatorsStack[count($operatorsStack) - 1] !== '(' &&
    getOperatorPriority($currentOperator) <= getOperatorPriority($operatorsStack[count($operatorsStack) - 1])
  ) {
    $operator = array_pop($operatorsStack);
    $right = array_pop($numbersStack);
    $left = array_pop($numbersStack);
    $result = calculate($operator, $left, $right);
    $numbersStack[] = $result;
  }
  $operatorsStack[] = $currentOperator;
  return [$numbersStack, $operatorsStack];
}

// Calculs et priorités

/**
 * Effectue le calcul pour deux nombres selon l'opérateur donné.
 */
function calculate($operator, $left, $right)
{
  switch ($operator):
    case "+":
      return $left + $right;
    case "-":
      return $left - $right;
    case "*":
      return $left * $right;
    case "/":
      return $left / $right;
    case "%":
      return $left % $right;
    default:
      return null;
  endswitch;
}

/**
 * Retourne la priorité d'un opérateur.
 * Les opérateurs * / % ont priorité 2, + - ont priorité 1.
 */
function getOperatorPriority($operator)
{
  switch ($operator):
    case "+":
    case "-":
      return 1;
    case "*":
    case "/":
    case "%":
      return 2;
    default:
      return null;
  endswitch;
}

// Fonctions de détection

/**
 * Vérifie si un caractère est un opérateur arithmétique.
 */
function isOperator($char)
{
  return in_array($char, ['+', '-', '*', '/', '%']);
}

/**
 * Vérifie si un caractère est un chiffre.
 */
function isDigit($char)
{
  return ctype_digit($char);
}

/**
 * Vérifie si un caractère est une parenthèse ouvrante ou fermante.
 */
function isParenthesis($char)
{
  return in_array($char, ['(', ')']);
}

// Analyseur de la chaîne

/**
 * Transforme une chaîne en un tableau de tokens (nombres, opérateurs, parenthèses).
 */
function tokenize($input)
{
  $tokens = [];
  $currentNumber = '';
  for ($i = 0; $i < strlen($input); $i++) {
    if (trim($input[$i]) == '') {
      continue;
    }
    if (isDigit($input[$i])) {
      $currentNumber .= $input[$i];
    } else {
      if ($currentNumber !== '') {
        $tokens[] = intval($currentNumber);
        $currentNumber = '';
      }
      if ($input[$i] === '(' || $input[$i] === ')') {
        $tokens[] = $input[$i];
      } elseif (isOperator($input[$i])) {
        $tokens[] = $input[$i];
      }
    }
  }
  if ($currentNumber !== '') {
    $tokens[] = intval($currentNumber);
  }
  return $tokens;
}

// Cœur de l'algorithme : évalue les tokens via deux piles

/**
 * Applique l'algorithme des piles pour calculer la valeur finale de l'expression.
 */
function evaluateExpression($tokens)
{
  $numbersStack = [];
  $operatorsStack = [];
  for ($i = 0; $i < count($tokens); $i++) {
    if (isParenthesis($tokens[$i])) {
      if ($tokens[$i] === "(") {
        $operatorsStack[] = $tokens[$i];
        continue;
      } elseif ($tokens[$i] === ")") {
        $returned = processClosingParenthesis($numbersStack, $operatorsStack);
        $numbersStack   = $returned[0];
        $operatorsStack = $returned[1];
      }
    } elseif (is_int($tokens[$i])) {
      $numbersStack[] = $tokens[$i];
    } elseif (isOperator($tokens[$i])) {
      $returned = processOperator($numbersStack, $operatorsStack, $tokens[$i]);
      $numbersStack   = $returned[0];
      $operatorsStack = $returned[1];
    }
  }
  $returned = flushOperatorsStack($numbersStack, $operatorsStack);
  $numbersStack   = $returned[0];
  $operatorsStack = $returned[1];
  return array_pop($numbersStack);
}

// Fonctions de validation des arguments

/**
 * Vérifie qu'il n'y a qu'un seul argument en entrée.
 */
function isValidArgCount($args)
{
  return count($args) === 1;
}

/**
 * Vérifie que l'argument est bien une chaîne de caractères.
 */
function isStringArgument($arg)
{
  return is_string($arg);
}

function showError()
{
  echo "error\n";
}
// Parsing

/**
 * Récupère la chaîne d'expression passée en argument au script.
 */
function parseArguments($argv)
{
  return array_slice($argv, 1);
}

// Résolution : appelle toutes les fonctions utiles dans l'ordre

function resolution($argv)
{
  $args = parseArguments($argv);
  if (!isValidArgCount($args)) {
    showError();
    return;
  }
  if (!isStringArgument($args[0])) {
    showError();
    return;
  }
  $inputString = $args[0];
  $tokens = tokenize($inputString);
  $result = evaluateExpression($tokens);
  displayResult($result);
}

// Affichage

/**
 * Affiche le résultat final dans le terminal.
 */
function displayResult($number)
{
  echo $number . "\n";
}

/**
 * Affiche une erreur générique.
 */

resolution($argv);
