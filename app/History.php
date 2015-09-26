<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'history';

  protected $fillable = array('user_id', 'date');
}
