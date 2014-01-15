WPxFramework
============

#This repo, and this readme, are far from complete.  Please excuse their untidy manner.#

Welcome to my first GitHub repo.  I'm a WordPress developer, and this is an OO framework that wraps WordPress Objects.

**Basic Usage**

```php
<?php

global $post; // this is the WP_Post object

// the general format for all WPx* classes:  
// Pass the WP_* object to the constructor

$wpx = new WPxPost($post);

echo $wpx->title(); // same as echo $post->post_title;

// WPx adds a display object to organize your views (HTML)
$wpx->display->list_item();
$wpx->author->display->author_card();

// WPxBase uses:
    // Chaining + get/set combo methods (like jQuery):
    // Each parameter function is a getter and a setter, depending on whether arguments have been passed
    $id = $wpx->title('New title')->content('New content')->save()->id();
    
    // When a function is called
        // 1st checks if the function exists, this is default PHP behavior
        // The parameter functions don't really exist, so it uses __call() magic method
        // When __call is executed, it checks if get_{param}() or set_{param} exists
        // If so, it uses that
        // If not, it just returns $this->{param} or sets $this->{param} = $value;
        // .. more on this in a second
```

**Two Asterisks**

    // this is indented
    function name(){
      // is this highlighted?
    }
    
*Single Asterisk*

```php
<?php
$this->that();
```

 - List Item
 - List Item
