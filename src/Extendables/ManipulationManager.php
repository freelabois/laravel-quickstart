<?php


namespace  Freelabois\LaravelQuickstart\Extendables;

use  Freelabois\LaravelQuickstart\Interfaces\ManipulationManagerInterface;
use  Freelabois\LaravelQuickstart\Interfaces\RepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ManipulationManager implements ManipulationManagerInterface
{
    const CREATE = 'create';
    const EDIT = 'edit';
    const CREATE_MESSAGE = 'create_message';
    const EDIT_MESSAGE = 'edit_message';
    protected $validation = [
        self::CREATE => [

        ],
        self::EDIT => [

        ],
        self::CREATE_MESSAGE => [

        ],
        self::EDIT_MESSAGE => [

        ],
    ];
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * ManipulationManager constructor.
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }


    /**
     * @param $values
     * @param int|null $id
     * @return mixed
     * @throws ValidationException
     */
    public function storeOrUpdate($values, int $id = null)
    {
        $this->validate($values, $id);
        return $this->persist($values, $id);
    }

    /**
     * @param $values
     * @param int|null $id
     * @throws ValidationException
     */
    public function validate($values, int $id = null)
    {
        if (isset($this->validation[self::EDIT]) && !empty($this->validation[self::EDIT])) {
            $validator = Validator::make(
                $values,
                $id ? $this->validation[self::EDIT] : $this->validation[self::CREATE],
                $id ? $this->validation[self::EDIT_MESSAGE] : $this->validation[self::CREATE_MESSAGE]
            );
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        }
    }

    /**
     * @param $values
     * @param int|null $id
     * @param array $relations
     * @return mixed
     */
    public function persist($values, int $id = null, array $relations = [])
    {
        return $this->repository->storeOrUpdate($values, $id, $relations);
    }

    public function destroy(int $id)
    {
        return $this->repository->destroy($id);
    }

}
