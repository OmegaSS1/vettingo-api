<?php

declare(strict_types=1);

namespace App\Traits;

use Exception;

trait MessageException
{
    public static function CPF(): Exception
    {
        return new class extends Exception {
            protected $message = "Por favor, insira um CPF válido.";
            protected $code = 400;
        };
    }

    public static function CNPJ(): Exception
    {
        return new class extends Exception {
            protected $message = "Por favor, insira um CNPJ válido.";
            protected $code = 400;
        };
    }

    public static function EMAIL(): Exception
    {
        return new class extends Exception {
            protected $message = "Por favor, insira um e-mail válido.";
            protected $code = 400;
        };
    }

    public static function PHONE(): Exception
    {
        return new class extends Exception {
            protected $message = "Por favor, insira um número de telefone válido.";
            protected $code = 400;
        };
    }

    public static function CELLPHONE(): Exception
    {
        return new class extends Exception {
            protected $message = "Por favor, insira um número de celular válido.";
            protected $code = 400;
        };
    }

    public static function CRMV(): Exception
    {
        return new class extends Exception {
            protected $message = "Por favor, insira um número de CRMV válido.";
            protected $code = 400;
        };
    }

    public static function CRMV_STATE_ID(): Exception
    {
        return new class extends Exception {
            protected $message = "Por favor, informe um Estado de CRMV válido.";
            protected $code = 400;
        };
    }

    public static function VETERINARIAN_NOT_FOUND(?int $id): Exception
    {
        return new class($id) extends Exception {
            public function __construct(?int $id){
                parent::__construct("Nenhum veterinário encontrado para o usuário $id", 404);
            }
        };
    }

    public static function PET_TYPE_NOT_FOUND(?int $id): Exception
    {
        return new class($id) extends Exception {
            public function __construct(?int $id){
                parent::__construct("Nenhum tipo de pet encontrado para o id $id", 404);
            }
        };
    }

    public static function PET_NOT_FOUND(?int $id): Exception
    {
        return new class($id) extends Exception {
            public function __construct(?int $id){
                parent::__construct("Nenhum pet encontrado para o id $id", 404);
            }
        };
    }

    public static function TUTOR_NOT_FOUND(?int $id): Exception
    {
        return new class($id) extends Exception {
            public function __construct(?int $id){
                parent::__construct("Nenhum tutor encontrado para o id $id", 404);
            }
        };
    }

    public static function USER_NOT_FOUND(?int $id): Exception
    {
        return new class($id) extends Exception {
            public function __construct(?int $id){
                parent::__construct("Nenhum usuario encontrado para o usuário $id", 404);
            }
        };
    }

    public static function TIME_INVALID(): Exception
    {
        return new class extends Exception {
            protected $message = "Por favor, informe um Horário válido (HH:MM:SS).";
            protected $code = 400;
        };
    }

    public static function START_TIME_BEFORE_END_TIME(): Exception
    {
        return new class extends Exception {
            protected $message = "Horário de início deve ser anterior ao horário de fim";
            protected $code = 400;
        };
    }

    public static function DAY_OF_WEEK_INVALID(): Exception
    {
        return new class extends Exception {
            protected $message = "Por favor, informe um dia da semans válido (0 a 6).";
            protected $code = 400;
        };
    }

    public static function VETERINARIAN_WORK_LOCATION_NOT_FOUND(): Exception
    {
        return new class extends Exception {
            protected $message = "Nenhum local de trabalho encontrado";
            protected $code = 404;
        };
    }

    public static function STATE_NOT_FOUND(?int $id): Exception
    {
        return new class($id) extends Exception {
            public function __construct(?int $id){
                parent::__construct("Estado com ID $id não encontrado.", 404);
            }
        };
    }

    public static function CITY_NOT_FOUND(?int $id): Exception
    {
        return new class($id) extends Exception {
            public function __construct(?int $id){
                parent::__construct("Cidade com ID $id não encontrado.", 404);
            }
        };
    }
    public static function CEP_INVALIDO(): Exception
    {
        return new class extends Exception {
            protected $message = "Por favor, informe um CEP válido.";
            protected $code = 400;
        };
    }

    public static function ALREADY_EXISTS(string $value): Exception
    {
        return new class($value) extends Exception {
            public function __construct(string $value){
                parent::__construct("O $value informado já está cadastrado. Verifique se os dados informados estão corretos.", 400);
            }
        };
    }

    public static function GENERIC(): Exception
    {
        return new class extends Exception {
            protected $message = "Ocorreu um erro inesperado. Tente novamente.";
            protected $code = 400;
        };
    }
}
