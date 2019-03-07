# Evénements Symfony, Doctrine

## Evénement(s) du Kernel

Au choix :


- Injecter du code HTML en haut des pages (par ex. une bannière informative pour une prochaine maintenance du site).

> instructions dans : EventSubscriber/MaintenanceSubscriber.php
> Note : Vous pouvez vous inspirer de RandomMovieSubscriber.php
 
### Ressources

- http://symfony.com/doc/current/event_dispatcher.html
- http://symfony.com/doc/current/event_dispatcher/before_after_filters.html

## Evénement(s) de formulaire

- Modifier le formulaire UserType pour :
    - password = NotBlank au create seulement
    - password, placeholder = 'Laissez vide si inchangé' uniquement à l'edit

> instructions dans : Form/UserType.php

### Ressources

- http://symfony.com/doc/current/form/events.html

## Evénement(s) Doctrine

- Slugifier le title lorsque l'entité Movie est créée ou modifiée.

> instructions dans : Entity/Movie.php


> Astuce: @ORM\HasLifecycleCallbacks() permet d'activer les evenements. Il est possible de réaliser des evenements déclenchables à l'intérieur d'une entité ou en externe à l'aide de Listeners / Subscribers.


### Ressources

- http://symfony.com/doc/current/doctrine/lifecycle_callbacks.html
- http://symfony.com/doc/current/doctrine/event_listeners_subscribers.html
