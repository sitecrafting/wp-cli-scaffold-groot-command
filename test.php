<?php

define('COMMENT_TOKENS', ['T_COMMENT', 'T_DOC_COMMENT']);

function is_groot_token($token) {
  return is_array($token)
    && in_array(token_name($token[0]), COMMENT_TOKENS)
    && strpos($token[1], '@groot') !== false;
}

function token_val($token) {
  return is_string($token) ? $token : $token[1];
}

function generate_hook_code($name, $code) {
  $hookGenerators = [
    'use_project_classes' => function() {
      return '/* USE MOAR CLASSEZ */';
    },
    'required_classes' => function($code) {
      return lines([
        "'\Some\Class',",
        "'\Some\Other\Class',"
      ], get_indentation_level($code));
    },
    'config_callback' => function($code) {
      $indentation = get_indentation_level($code);

      return lines(['// your custom code goes here'], $indentation);
    },
  ];

  $default = function() { return ''; };

  $generator = $hookGenerators[$name] ?? $default;

  return $generator($code);
}

function lines(array $lines, $indentation = 0) {
  $indent = str_repeat(' ', $indentation);
  return implode("\n$indent", $lines);
}

function get_indentation_level(string $comment) : int {
  $lines = explode("\n", $comment);
  if (count($lines) < 2) {
    return 0;
  }

  // get the chars of the first indented line
  $chars = str_split($lines[1]);

  $spaces = 0;
  while ($char = array_shift($chars)) {
    if ($char !== ' ') {
      break;
    }

    $spaces++;
  }

  return floor($spaces / 2) * 2;
}

function get_groot_hook_name(string $tokenStr) {
  // collapse @groot hook to an array of non-empty "words"
  $words = get_groot_hook_components($tokenStr);
  foreach($words as $i => $word) {
    if ($word === '@groot') {
      // we found @groot tag, return the word directly after it
      return $words[$i + 1] ?? '';
    }
  }

  return '';
}

function get_groot_hook_components(string $tokenStr) : array {
  $trimmed = array_map('trim', explode(' ', $tokenStr));
  return array_values(array_filter($trimmed));
}

$php = file_get_contents('./test-src.php');

$tokens = token_get_all($php);

$generated = [];
while ($token = array_shift($tokens)) {
  if (is_groot_token($token)) {
    $generated[] = generate_hook_code(
      get_groot_hook_name($token[1]),
      $token[1]
    );
  } else {
    $generated[] = token_val($token);
  }
}

echo implode('', $generated);
