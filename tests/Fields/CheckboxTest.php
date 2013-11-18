<?php
class CheckboxTest extends FormerTests
{

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// MATCHERS ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Matches a checkbox
   *
   * @param  string   $name
   * @param  string   $label
   * @param  integer  $value
   * @param  boolean  $inline
   * @param  boolean  $checked
   *
   * @return string
   */
  private function matchCheckbox($name = 'foo', $label = null, $value = 1, $inline = false, $checked = false)
  {
    $checkAttr = array(
      'id'      => $name,
      'type'    => 'checkbox',
      'name'    => $name,
      'checked' => 'checked',
      'value'   => $value,
    );
    $labelAttr = array(
      'for'   => $name,
      'class' => 'checkbox',
    );
    if ($inline) $labelAttr['class'] .= ' inline';
    if (!$checked) unset($checkAttr['checked']);

    $radio = '<input'.$this->attributes($checkAttr).'>';

    return $label ? '<label'.$this->attributes($labelAttr). '>' .$radio.$label. '</label>' : $radio;
  }

  /**
   * Matches a checked checkbox
   *
   * @param  string  $name
   * @param  string  $label
   * @param  integer $value
   * @param  boolean $inline
   *
   * @return string
   */
  private function matchCheckedCheckbox($name = 'foo', $label = null, $value = 1, $inline = false)
  {
    return $this->matchCheckbox($name, $label, $value, $inline, true);
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////////// TESTS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  public function testCanCreateASingleCheckedCheckbox()
  {
    $checkbox = $this->former->checkbox('foo')->__toString();
    $matcher = $this->controlGroup($this->matchCheckbox('foo'));

    $this->assertEquals($matcher, $checkbox);
  }

  public function testCanCreateACheckboxWithALabel()
  {
    $checkbox = $this->former->checkbox('foo')->text('bar')->__toString();
    $matcher = $this->controlGroup($this->matchCheckbox('foo', 'Bar'));

    $this->assertEquals($matcher, $checkbox);
  }

  public function testCanSetValueOfASingleCheckbox()
  {
    $checkbox = $this->former->checkbox('foo')->text('bar')->value('foo')->__toString();
    $matcher = $this->controlGroup($this->matchCheckbox('foo', 'Bar', 'foo'));

    $this->assertEquals($matcher, $checkbox);
  }

  public function testCanCreateMultipleCheckboxes()
  {
    $checkboxes = $this->former->checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->controlGroup($this->matchCheckbox('foo_0', 'Foo').$this->matchCheckbox('foo_1', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testCanFocusOnACheckbox()
  {
    $checkboxes = $this->former->checkboxes('foo')
      ->checkboxes('foo', 'bar')
      ->on(0)->text('first')->on(1)->text('second')->__toString();

    $matcher = $this->controlGroup($this->matchCheckbox('foo_0', 'First').$this->matchCheckbox('foo_1', 'Second'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testCanCreateInlineCheckboxes()
  {
    $checkboxes1 = $this->former->inline_checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $this->resetLabels();
    $checkboxes2 = $this->former->checkboxes('foo')->inline()->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->controlGroup($this->matchCheckbox('foo_0', 'Foo', 1, true).$this->matchCheckbox('foo_1', 'Bar', 1, true));

    $this->assertEquals($matcher, $checkboxes1);
    $this->assertEquals($matcher, $checkboxes2);
  }

  public function testCanCreateStackedCheckboxes()
  {
    $checkboxes1 = $this->former->stacked_checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $this->resetLabels();
    $checkboxes2 = $this->former->checkboxes('foo')->stacked()->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->controlGroup($this->matchCheckbox('foo_0', 'Foo', 1).$this->matchCheckbox('foo_1', 'Bar', 1));

    $this->assertEquals($matcher, $checkboxes1);
    $this->assertEquals($matcher, $checkboxes2);
  }

  public function testCanCreateMultipleCheckboxesViaAnArray()
  {
    $this->resetLabels();
    $checkboxes = $this->former->checkboxes('foo')->checkboxes(array('Foo' => 'foo', 'Bar' => 'bar'))->__toString();
    $matcher = $this->controlGroup($this->matchCheckbox('foo', 'Foo').$this->matchCheckbox('bar', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testCanCustomizeCheckboxesViaAnArray()
  {
    $checkboxes = $this->former->checkboxes('foo')->checkboxes($this->checkables)->__toString();
    $matcher = $this->controlGroup(
    '<label for="foo" class="checkbox">'.
      '<input data-foo="bar" value="bar" id="foo" type="checkbox" name="foo">'.
      'Foo'.
    '</label>'.
    '<label for="bar" class="checkbox">'.
      '<input data-foo="bar" value="bar" id="bar" type="checkbox" name="foo">'.
      'Bar'.
    '</label>');

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testCanCreateMultipleAnonymousCheckboxes()
  {
    $checkables = $this->checkables;
    unset($checkables['Foo']['name']);
    unset($checkables['Bar']['name']);

    $checkboxes = $this->former->checkboxes('foo')->checkboxes($checkables)->__toString();
    $matcher = $this->controlGroup(
    '<label for="foo_0" class="checkbox">'.
      '<input data-foo="bar" value="bar" id="foo_0" type="checkbox" name="foo_0">'.
      'Foo'.
    '</label>'.
    '<label for="bar" class="checkbox">'.
      '<input data-foo="bar" value="bar" id="bar" type="checkbox" name="foo_1">'.
      'Bar'.
    '</label>');

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testCanCheckASingleCheckbox()
  {
    $checkbox = $this->former->checkbox('foo')->check()->__toString();
    $matcher = $this->controlGroup($this->matchCheckedCheckbox());

    $this->assertEquals($matcher, $checkbox);
  }

  public function testCanCheckOneInSeveralCheckboxes()
  {
    $checkboxes = $this->former->checkboxes('foo')->checkboxes('foo', 'bar')->check('foo_1')->__toString();
    $matcher = $this->controlGroup($this->matchCheckbox('foo_0', 'Foo').$this->matchCheckedCheckbox('foo_1', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testCanCheckMultipleCheckboxesAtOnce()
  {
    $checkboxes = $this->former->checkboxes('foo')->checkboxes('foo', 'bar')->check(array('foo_0' => false, 'foo_1' => true))->__toString();
    $matcher = $this->controlGroup($this->matchCheckbox('foo_0', 'Foo').$this->matchCheckedCheckbox('foo_1', 'Bar', 1));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testCanRepopulateCheckboxesFromPost()
  {
    $this->request->shouldReceive('get')->with('_token', '', true)->andReturn('');
    $this->request->shouldReceive('get')->with('foo', '', true)->andReturn('');
    $this->request->shouldReceive('get')->with('foo_0', '', true)->andReturn(true);
    $this->request->shouldReceive('get')->with('foo_1', '', true)->andReturn(false);

    $checkboxes = $this->former->checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->controlGroup($this->matchCheckedCheckbox('foo_0', 'Foo').$this->matchCheckbox('foo_1', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testCanPopulateCheckboxesFromAnObject()
  {
    $this->former->populate((object) array('foo_0' => true));

    $checkboxes = $this->former->checkboxes('foo')->checkboxes('foo', 'bar')->__toString();
    $matcher = $this->controlGroup($this->matchCheckedCheckbox('foo_0', 'Foo').$this->matchCheckbox('foo_1', 'Bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testCanPopulateCheckboxesWithRelations()
  {
    $eloquent = new DummyEloquent(array('id' => 1, 'name' => 3));

    $this->former->populate($eloquent);
    $checkboxes = $this->former->checkboxes('roles')->__toString();
    $matcher = $this->controlGroup(
      $this->matchCheckbox('1', 'Foo').$this->matchCheckbox('3', 'Bar'),
      '<label for="roles" class="control-label">Roles</label>');

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testCanDecodeCorrectlyCheckboxes()
  {
    $checkbox = $this->former->checkbox('foo')->__toString();

    $content = html_entity_decode($checkbox, ENT_QUOTES, 'UTF-8');

    $this->assertEquals($content, $this->former->checkbox('foo')->__toString());
  }

  public function testCanPushUncheckedCheckboxes()
  {
    $this->config = $this->mockConfig(true, '', false);

    $checkbox = $this->former->checkbox('foo')->text('foo')->push(true);
    $matcher  = $this->controlGroup(
      '<label for="foo" class="checkbox">'.
        '<input type="hidden" name="foo" value="">'.
        $this->matchCheckbox('foo').'Foo'.
      '</label>');

    $this->assertEquals($matcher, $checkbox->wrapAndRender());
  }

  public function testCanPushASingleCheckbox()
  {
    $this->config = $this->mockConfig(true, '', true);

    $checkbox = $this->former->checkbox('foo')->text('foo')->__toString();
    $matcher = $this->controlGroup(
      '<label for="foo" class="checkbox">'.
        '<input type="hidden" name="foo" value="">'.
        $this->matchCheckbox('foo').'Foo'.
      '</label>');

    $this->assertEquals($matcher, $checkbox);
  }

  public function testCanRepopulateCheckboxesOnSubmit()
  {
    $this->config = $this->mockConfig(true, '', true);
    $this->request->shouldReceive('get')->andReturn('');

    $checkbox = $this->former->checkbox('foo')->text('foo')->__toString();
    $matcher = $this->controlGroup(
      '<label for="foo" class="checkbox">'.
        '<input type="hidden" name="foo" value="">'.
        $this->matchCheckbox('foo').'Foo'.
      '</label>');

    $this->assertEquals($matcher, $checkbox);
  }

  public function testCanGroupCheckboxes()
  {
    $this->former->framework('Nude');

    $auto =  $this->former->checkboxes('value[]', '')->checkboxes('Value 01', 'Value 02')->__toString();
    $chain = $this->former->checkboxes('value', '')->grouped()->checkboxes('Value 01', 'Value 02')->__toString();

    $this->assertEquals($chain, $auto);
    $this->assertEquals(
      '<label for="value_0" class="checkbox">'.
        '<input id="value_0" type="checkbox" name="value[]" value="1">Value 01'.
      '</label>'.
      '<label for="value_1" class="checkbox">'.
        '<input id="value_1" type="checkbox" name="value[]" value="1">Value 02'.
      '</label>', $auto);
  }

  public function testCanRepopulateGroupedCheckboxes()
  {
    $this->former->framework('Nude');
    $this->former->populate(array('foo_0' => 1, 'foo_1' => 1)); // verify we can check multiple checkboxes

    $chain = $this->former->checkboxes('foo', '')->grouped()->checkboxes('Value 01', 'Value 02', 'Value 03')->__toString();
    $auto = $this->former->checkboxes('foo[]', '')->checkboxes('Value 01', 'Value 02', 'Value 03')->__toString();

    $matcher =
      '<label for="foo_0" class="checkbox">'.
        '<input id="foo_0" type="checkbox" name="foo[]" checked="checked" value="1">Value 01'.
      '</label>'.
      '<label for="foo_1" class="checkbox">'.
        '<input id="foo_1" type="checkbox" name="foo[]" checked="checked" value="1">Value 02'.
      '</label>'.
      '<label for="foo_2" class="checkbox">'.
        '<input id="foo_2" type="checkbox" name="foo[]" value="1">Value 03'.
      '</label>';

    $this->assertEquals($matcher, $chain);
    $this->assertEquals($matcher, $auto);
  }

  public function testCanCustomizeTheUncheckedValue()
  {
    $this->config = $this->mockConfig(true, 'unchecked', true);

    $checkbox = $this->former->checkbox('foo')->text('foo')->__toString();
    $matcher = $this->controlGroup(
      '<label for="foo" class="checkbox">'.
        '<input type="hidden" name="foo" value="unchecked">'.
        $this->matchCheckbox('foo').'Foo'.
      '</label>');

    $this->assertEquals($matcher, $checkbox);
  }

  public function testCanRecognizeGroupedCheckboxesValidationErrors()
  {
    $this->session = $this->mockSession(array('foo' => 'bar', 'bar' => 'baz'));
    $this->former->withErrors();

    $auto =  $this->former->checkboxes('foo[]', '')->checkboxes('Value 01', 'Value 02')->__toString();
    $chain = $this->former->checkboxes('foo', '')->grouped()->checkboxes('Value 01', 'Value 02')->__toString();

    $matcher =
      '<div class="control-group error">'.
      '<div class="controls">'.
      '<label for="foo_0" class="checkbox">'.
      '<input id="foo_0" type="checkbox" name="foo[]" value="0">Value 01'.
      '</label>'.
      '<label for="foo_1" class="checkbox">'.
      '<input id="foo_1" type="checkbox" name="foo[]" value="1">Value 02'.
      '</label>'.
      '<span class="help-inline">bar</span>'.
      '</div>'.
      '</div>';

    $this->assertEquals($matcher, $auto);
    $this->assertEquals($matcher, $chain);
  }

  public function testCanHandleAZeroUncheckedValue()
  {
    $this->config = $this->mockConfig(true, 0);
    $checkboxes = $this->former->checkboxes('foo')->value('bar')->__toString();
    $matcher = $this->controlGroup($this->matchCheckbox('foo', null, 'bar'));

    $this->assertEquals($matcher, $checkboxes);
  }

  public function testRepopulatedValueDoesntChangeOriginalValue()
  {
    $this->markTestSkipped('Test reformulated proves opposite of that stated');

    $this->former->populate(array('foo' => 'bar'));
    $checkboxTrue = $this->former->checkbox('foo')->__toString();
    $matcherTrue = $this->controlGroup($this->matchCheckedCheckbox());

    $this->assertEquals($matcherTrue, $checkboxTrue);

    $this->former->populate(array('foo' => 'baz'));
    $checkboxFalse = $this->former->checkbox('foo')->__toString();
    $matcherFalse = $this->controlGroup($this->matchCheckbox());

    $this->assertEquals($matcherFalse, $checkboxFalse);
  }

  public function testCanPushCheckboxesWithoutLabels()
  {
    $this->config = $this->mockConfig(true, '', true, false);

    $html  = $this->former->label('<b>Views per Page</b>')->render();
    $html .= $this->former->checkbox('per_page')->class('input')->render();

    $this->assertInternalType('string', $html);
  }

}
