<div id="content-page" class="content group">
   <div class="hentry group">

    {!! Form::open(['url' => (isset($menu->id)) ? route('admin.menus.update', ['menus' => $menu->id]) : route('admin.menus.store'), 'class' => 'contact-form', 'method' => 'POST' ]) !!}

    <ul>
        <li class="text-field">
            <label for="name-contact-us">
                <span class="label">Titles:</span>
                <br>
                <span class="sublabel">Title menu</span>
                <br>
            </label>
            <div class="input-prepend">
                {!! Form::text('title', isset($menu->title) ? $menu->title : old('title'), ['placeholder' => 'Put menu name']) !!}
             </div>
        </li>

        <li class="text-field">
            <label for="name-contact-us">
                <span class="label">Parent menu item:</span>
                <br />
                <span class="sublabel">Parent</span><br />
            </label>
            <div class="input-prepend">
            {!! Form::select('parent_id', $menus, isset($menu->parent_id) ? $menu->parent_id  : null) !!}
             </div>
        </li>
    </ul>

    <h1>Menu type:</h1>

    <div id="accordion">
        <h3>{!! Form::radio('type', 'customLink', (isset($type) && $type == 'customLink') ? TRUE : FALSE, ['class' => 'radioMenu']) !!}
            <span class="label">Custom Link:</span>
        </h3>
        <ul>
            <li class="text-field">
                <label for="name-contact-us">
                    <span class="label">Path for link:</span>
                    <br />
                    <span class="sublabel">Заголовок материала</span><br />
                </label>
                <div class="input-prepend">
                {!! Form::text('custom_link', (isset($menu->path) && $type == 'customLink') ? $menu->path : old('custom_link'), ['placeholder' => 'Введите название страницы']) !!}
                 </div>
            </li>
            <div style="clear: both;"></div>
        </ul>
    
        <h3>{!! Form::radio('type', 'blogLink', (isset($type) && $type == 'blogLink') ? TRUE : FALSE, ['class' => 'radioMenu']) !!}
            <span class="label">Blog section:</span>
        </h3>
        <ul>
            <li class="text-field">
                <label for="name-contact-us">
                    <span class="label">Link for blog category:</span>
                    <br />
                    <span class="sublabel">Ссилка на категорію блогу</span><br />
                </label>
                <div class="input-prepend">
                    @if($categories)
                        {!! Form::select('category_alias', $categories, (isset($option) && $type == 'blogLink' && $option) ? $option : FALSE) !!}
                    @endif
                 </div>
            </li> 
            <li class="text-field">
                <label for="name-contact-us">
                    <span class="label">Link for article category:</span>
                    <br />
                    <span class="sublabel">Ссилка на статтю блогу</span><br />
                </label>
                <div class="input-prepend"><span class="add-on"></span>
                {!! Form::select('article_alias', $articles, (isset($option) && $type == 'blogLink' && $option) ? $option : FALSE, ['placeholder' => 'Not used']) !!}
                 </div>
            </li>
            <div style="clear: both;"></div>
        </ul>

        <h3>{!! Form::radio('type', 'portfolioLink', (isset($type) && $type == 'portfolioLink') ? TRUE : FALSE, ['class' => 'radioMenu']) !!}
            <span class="label">Portfolio section:</span>
        </h3>
        <ul>
            <li class="text-field">
                <label for="name-contact-us">
                    <span class="label">Link for blog portfolio:</span>
                    <br />
                    <span class="sublabel">Ссилка на портфоліо блогу</span><br />
                </label>
                <div class="input-prepend"><span class="add-on"></span>
                {!! Form::select('portfolio_alias', $portfolios, (isset($option) && $type == 'portfolioLink' && $option) ? $option : FALSE, ['placeholder' => 'Not used']) !!}
                 </div>
            </li> 
            <li class="text-field">
                <label for="name-contact-us">
                    <span class="label">Portfolio:</span>
                    <br />
                    <span class="sublabel">Portfolio</span><br />
                </label>
                <div class="input-prepend"><span class="add-on"></span>
                {!! Form::select('filter_alias', $filters, (isset($option) && $type == 'portfolioLink' && $option) ? $option : FALSE, ['placeholder' => 'Not used']) !!}
                 </div>
            </li>
            <div style="clear: both;"></div>
        </ul>
    </div>
    <br>

    @if(isset($menu->id))
        <input type="hidden" name="_method" value="PUT">
    @endif

    <ul>
        <li class="submit-button">
            {!! Form::button('Save', ['class' => 'btn btn-the-salmon-dance-3', 'type' => 'submit']) !!}
        </li>
    </ul>

    {!! Form::close() !!}
   </div>
</div>

<script>
    jQuery(function($) {
        $('#accordion').accordion({
            activate: function(e, obj) {
                obj.newPanel.prev().find('input[type=radio]').attr('checked', 'checked');
            }
        });

        var active = 0;
        $('#accordion input[type=radio]').each(function(ind, it) {
            if($(this).prop('checked')) {
                active = ind;
            }
        });

        $('#accordion').accordion('option', 'active', active);
    })
</script>