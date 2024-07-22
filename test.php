<?php

enum RequestType {
  case Submit;
  case NavigateBack;

  public function toString(): string {
    return match($this) {
      RequestType::Submit => 'grey',
      RequestType::NavigateBack => 'green',
    };
  }
}



echo RequestType::Submit->toString();