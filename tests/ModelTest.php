<?php

declare(strict_types=1);

namespace App\Tests;

use App\System\Model\DbDriver;
use App\System\Model\Model;
use App\Tests\Fixtures\Widget;
use PHPUnit\Framework\TestCase;

final class ModelTest extends TestCase
{
    protected function setUp(): void
    {
        $pdo = new \PDO('sqlite::memory:');
        $pdo->exec('CREATE TABLE widgets (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT)');

        Model::setConnection(DbDriver::fromPdo($pdo));
    }

    public function testInsertReturnsNewId(): void
    {
        $widget = new Widget();
        $widget->name = 'gizmo';

        self::assertSame('1', $widget->save());
    }

    public function testFindReturnsInsertedRow(): void
    {
        $widget = new Widget();
        $widget->name = 'gizmo';
        $id = $widget->save();

        $found = Widget::find((int) $id);

        self::assertSame('gizmo', $found['name']);
    }

    public function testFindReturnsNullWhenMissing(): void
    {
        self::assertNull(Widget::find(999));
    }

    public function testAllReturnsEveryRow(): void
    {
        $first = new Widget();
        $first->name = 'one';
        $first->save();

        $second = new Widget();
        $second->name = 'two';
        $second->save();

        self::assertCount(2, Widget::all());
    }

    public function testWhereBindsValuesInsteadOfConcatenatingThem(): void
    {
        $widget = new Widget();
        $widget->name = "o'brien; DROP TABLE widgets; --";
        $widget->save();

        $rows = (new Widget())->select('*')->where('name', "o'brien; DROP TABLE widgets; --")->get();

        self::assertCount(1, $rows);
        // the table must still exist: a string-concatenated query would have dropped it
        self::assertCount(1, Widget::all());
    }

    public function testUpdatePersistsChanges(): void
    {
        $widget = new Widget();
        $widget->name = 'gizmo';
        $id = (int) $widget->save();

        $loaded = new Widget($id);
        $loaded->name = 'renamed-gizmo';
        $loaded->save();

        self::assertSame('renamed-gizmo', Widget::find($id)['name']);
    }

    public function testDestroyRemovesRow(): void
    {
        $widget = new Widget();
        $widget->name = 'gizmo';
        $id = (int) $widget->save();

        (new Widget($id))->destroy();

        self::assertNull(Widget::find($id));
    }
}
