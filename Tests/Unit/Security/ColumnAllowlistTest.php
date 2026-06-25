<?php

declare(strict_types=1);

namespace HRR\T3Datatable\Tests\Unit\Security;

use HRR\T3Datatable\DataTable\GridDefinition;
use HRR\T3Datatable\Exception\InvalidColumnException;
use HRR\T3Datatable\Security\ColumnAllowlist;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ColumnAllowlistTest extends TestCase
{
    #[Test]
    public function allowsDeclaredSearchableColumns(): void
    {
        $definition = (new GridDefinition())
            ->addColumn('title', 'Title', searchable: true, orderable: true);

        $allowlist = new ColumnAllowlist($definition);
        $allowlist->assertSearchable('title');

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function rejectsUndeclaredColumns(): void
    {
        $definition = (new GridDefinition())
            ->addColumn('title', 'Title', searchable: true, orderable: true);

        $allowlist = new ColumnAllowlist($definition);

        $this->expectException(InvalidColumnException::class);
        $allowlist->assertSearchable('password');
    }

    #[Test]
    public function rejectsInvalidIdentifiers(): void
    {
        $definition = (new GridDefinition())
            ->addColumn('title', 'Title', searchable: true, orderable: true);

        $allowlist = new ColumnAllowlist($definition);

        $this->expectException(InvalidColumnException::class);
        $allowlist->assertOrderable('title; DROP TABLE');
    }
}
