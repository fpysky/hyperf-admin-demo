<?php

declare(strict_types=1);

namespace App\Service;

use App\Extend\CacheRule;
use App\Model\Dto\RuleDto;
use App\Model\Rule;
use Hyperf\Database\Model\Collection;
use Hyperf\Di\Annotation\Inject;

class RuleService
{
    #[Inject]
    protected CacheRule $cacheRule;

    public function create(RuleDto $dto): void
    {
        $rule = new Rule();
        $this->saveData($rule, $dto);

        $this->cacheRule->asyncRemoveCache();
    }

    public function update(Rule $rule, RuleDto $dto): void
    {
        $this->saveData($rule, $dto);

        $this->cacheRule->asyncRemoveCache();
    }

    public function delete(array $ids): void
    {
        Rule::query()
            ->whereIn('id', $ids)
            ->delete();

        $this->cacheRule->asyncRemoveCache();
    }

    public function getRuleTree(): array|Collection|\Hyperf\Collection\Collection
    {
        return Rule::query()
            ->where('parent_id', 0)
            ->with([
                'children' => function ($query) {
                    $query->with('children')
                        ->orderByDesc('type')
                        ->orderBy('sort');
                },
            ])
            ->orderBy('sort')
            ->get();
    }

    private function saveData(Rule $rule, RuleDto $dto): void
    {
        $rule->parent_id = $dto->parentId;
        $rule->status = $dto->status;
        $rule->type = $dto->type;
        $rule->sort = $dto->sort;
        $rule->name = $dto->name;
        $rule->icon = $dto->icon;
        $rule->route = $dto->route;
        $rule->path = $dto->path;
        $rule->save();
    }
}
