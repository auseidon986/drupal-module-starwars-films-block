<?php

namespace Drupal\starwars\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Serialization\Json;

/**
 * Provides a Starwars Films Block.
 *
 * @Block(
 *   id = "starwars_films_block",
 *   admin_label = @Translation("Starwars Films"),
 *   category = @Translation("Starwars Films"),
 * )
 */
class StarwarsFilmsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    
    $headline = $this->configuration['starwars_film_block_name'] ?? 'Starwas Films';

    return [
      '#theme' => 'starwars_films_block',
      '#headline' => $headline,
      '#films' => $this->loadFilmList(),
    ];
  }

  private function loadFilmList() {
    
    $numbers = $this->configuration['starwars_film_numbers'] ?? '1,2,3';
    $numbers = explode(',', $numbers);
    $films = array();

    $client = \Drupal::httpClient();

    foreach ($numbers AS $n) {
      $n = intval($n);
      if ($n == 0) continue;
      
      $url = 'https://swapi.dev/api/films/' . $n . '/?format=json';

      try {
        $request = $client->get($url);
        $status = $request->getStatusCode();
        $film = Json::decode($request->getBody()->getContents());
        if ($film) $films[] = $film;
      }
      catch (RequestException $e) {
        //An error happened.
      }

    }

    return $films;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['starwars_film_block_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Block Title'),
      '#description' => $this->t('Describe title of the block'),
      '#default_value' => $config['starwars_film_block_name'] ?? '',
    ];

    $form['starwars_film_numbers'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Film IDs seperated by comma'),
      '#description' => $this->t('Put film numbers seperated by comma.'),
      '#default_value' => $config['starwars_film_numbers'] ?? '1,2,3',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['starwars_film_block_name'] = $values['starwars_film_block_name'];
    $this->configuration['starwars_film_numbers'] = $values['starwars_film_numbers'];
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    // put some validate here
  }

}