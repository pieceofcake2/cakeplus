<?php

App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('HtmlEscapeComponent', 'Cakeplus.Controller/Component');

class HtmlEscapeComponentTest extends CakeTestCase
{
    /**
     * @var HtmlEscapeComponent
     */
    public $HtmlEscape;

    public function setUp(): void
    {
        parent::setUp();
        $this->HtmlEscape = new HtmlEscapeComponent(new ComponentCollection());
    }

    public function tearDown(): void
    {
        unset($this->HtmlEscape);
        parent::tearDown();
    }

    /**
     * The component can be loaded through a ComponentCollection
     *
     * @return void
     */
    public function testLoad(): void
    {
        $Collection = new ComponentCollection();
        $Collection->init(new Controller());
        $component = $Collection->load('Cakeplus.HtmlEscape');

        $this->assertInstanceOf(HtmlEscapeComponent::class, $component);
    }

    /**
     * nl2br_h() escapes strings and converts newlines
     *
     * @return void
     */
    public function testNl2brHString(): void
    {
        $result = $this->HtmlEscape->nl2br_h("<b>a</b>\nb &amp; c");

        $this->assertSame("&lt;b&gt;a&lt;/b&gt;<br />\nb &amp;amp; c", $result);
    }

    /**
     * nl2br_h() uses the given charset
     *
     * @return void
     */
    public function testNl2brHCharset(): void
    {
        $this->assertSame('&lt;a&gt;', $this->HtmlEscape->nl2br_h('<a>', 'UTF-8'));
    }

    /**
     * nl2br_h() walks arrays and skips the keys in $noescape_list
     *
     * @return void
     */
    public function testNl2brHArray(): void
    {
        $data = [
            ['Post' => ['title' => '<i>title</i>', 'body' => "<p>body</p>\nend"]],
        ];
        $result = $this->HtmlEscape->nl2br_h($data, null, ['Post.title']);

        $this->assertSame('<i>title</i>', $result[0]['Post']['title']);
        $this->assertSame("&lt;p&gt;body&lt;/p&gt;<br />\nend", $result[0]['Post']['body']);
    }
}
