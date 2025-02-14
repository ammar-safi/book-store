<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class BooksData extends Data
{
  public function __construct(
    #[Rule("required")]
    public $title,
    public $uuid,
    public $author,
    public $cover,
    public $description,
    public $book,
  ) {

  }
}
