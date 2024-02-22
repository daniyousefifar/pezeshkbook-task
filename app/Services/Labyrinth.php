<?php

namespace App\Services;

use App\Models\LabyrinthBlock;
use App\Models\Labyrinth as LabyrinthModel;

class Labyrinth
{
    protected array $activeBlocks = [];

    protected array $visitedBlocks = [];

    protected array $solution = [];

    protected LabyrinthModel $model;

    public function __construct(LabyrinthModel $model)
    {
        $this->model = $model;

        $firstBlock = $this->model->blocks()->where(['x' => $model->start['x'], 'y' => $model->start['y']])->first();

        $this->activeBlocks[] = $firstBlock;

        $this->visitedBlocks[] = $firstBlock->id;

        $this->recurse();
    }

    protected function getDirections(): array
    {
        return [
            'n',
            's',
            'e',
            'w',
        ];
    }

    protected function getDirectionOffset($direction): array
    {
        $data = [
            'n' => [
                'x' => 0,
                'y' => -1,
            ],
            's' => [
                'x' => 0,
                'y' => 1,
            ],
            'e' => [
                'x' => -1,
                'y' => 0,
            ],
            'w' => [
                'x' => 1,
                'y' => 0,
            ],
        ];

        return $data[$direction];
    }

    protected function getDirectionTranslation($direction): string
    {
        $data = [
            'n' => 'up',
            's' => 'down',
            'e' => 'left',
            'w' => 'right',
        ];

        return $data[$direction];
    }

    protected function getBlockDirections($x, $y): array
    {
        $result = [];

        foreach ($this->getDirections() as $dir) {
            $block = $this->model->blocks()->where([
                'x' => $x + $this->getDirectionOffset($dir)['x'],
                'y' => $y + $this->getDirectionOffset($dir)['y'],
                'passable' => true,
            ])->first();

            if ($block instanceof LabyrinthBlock) {
                $result[] = $dir;
            }
        }

        return $result;
    }

    protected function generateRandomNumber(): float
    {
        return (float)rand() / (float)getrandmax();
    }

    protected function recurse(): void
    {
        while (count($this->activeBlocks) > 0) {
            $currentBlock = $this->activeBlocks[count($this->activeBlocks) - 1];

            if (
                $currentBlock->x == $this->model->end['x'] &&
                $currentBlock->y == $this->model->end['y']
            ) {
                break;
            }

            $potentialDirections = $this->getBlockDirections($currentBlock->x, $currentBlock->y);

            $validDirections = [];

            foreach ($potentialDirections as $dir) {
                $block = $this->model->blocks()->where([
                    'x' => $currentBlock->x + $this->getDirectionOffset($dir)['x'],
                    'y' => $currentBlock->y + $this->getDirectionOffset($dir)['y'],
                    'passable' => true,
                ])->first();

                if ($block instanceof LabyrinthBlock && !in_array($block->id, $this->visitedBlocks)) {
                    $validDirections[] = $dir;
                }
            }

            if (count($validDirections) < 1) {
                array_pop($this->activeBlocks);

                array_pop($this->solution);
            } else {
                $randomIndex = floor($this->generateRandomNumber() * count($validDirections));
                $randomDirection = $validDirections[$randomIndex];

                $newBlock = $this->model->blocks()->where([
                    'x' => $currentBlock->x + $this->getDirectionOffset($randomDirection)['x'],
                    'y' => $currentBlock->y + $this->getDirectionOffset($randomDirection)['y']
                ])->first();

                if (!$newBlock instanceof LabyrinthBlock) {
                    throw new \Exception('Invalid Next Block');
                }

                $this->activeBlocks[] = $newBlock;
                $this->visitedBlocks[] = $newBlock->id;

                $this->solution[] = $this->getDirectionTranslation($randomDirection);
            }

            $this->recurse();
        }
    }

    public function getSolution(): array
    {
        return $this->solution;
    }
}
