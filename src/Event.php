<?php

namespace Gargantua;

enum RequestType: string {
  case Submit;
  case NavigateBack;

  public function toString(): string
  {
      return match($this)
      {
        RequestType::Submit => 'submit',
        RequestType::NavigateBack => 'navigate:back',
      };
  }
}


enum Event: string {
  case Next;
}
