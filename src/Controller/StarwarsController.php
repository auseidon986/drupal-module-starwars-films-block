<?php

namespace Drupal\starwars\Controller;

class StarwarsController {
  public function index() {
    return array(
      '#title' => 'Starwars',
      '#markup' => 'Starwars, I love it!'
    );
  }
}



