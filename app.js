$(document).ready(function() {
    var category_tmpl = $('div#grade-category-tmpl').find('table'),
        item_tmpl = $('div#grade-item-tmpl').find('tr'),
        weight_tmpl = $('div#category-weight-tmpl').find('div:first');

    function get_selected(selector) {
        return $(selector).children().filter(':selected').html();
    }

    function add_category(name, weight) {
        var category = category_tmpl.clone();

        category.attr('name', name);

        if (name === '') {
            return;
        }

        category.find('h3').replaceWith('<h3><span>' + name + '</span> <span class="label label-important remove-category-label">X &nbsp;Remove</span></h3>');

        $('div#grade-categories').append(category);

        $('select#add-item-category').append('<option>' + name + '</option>');

        $('fieldset').append(weight_tmpl
            .clone().attr('name', name)
            .find('span:first').replaceWith('<span>' + name + '</span>')
        );

        if (weight) {
            $('.control-group[name="' + name + '"]').find('input').val(weight);
        }
    }

    // Add grade category
    $('button#add-category').click(function(e) {
        e.preventDefault();

        var name = $('input#category-name').val();

        add_category(name);
    });

    function add_item(category_name, name, points) {
        var category = $('table[name="' + category_name + '"]').find('tbody'),
            to_add = $('input#grade-item-num-add').val(),
            i = 0;

        for (; i < to_add; i++) {
            var item = item_tmpl.clone();

            if (!name) {
                var num = category.children().length + 1,
                    itemname = category_name + ' ' + num;
            } else {
                var itemname = name;
            }

            item.find('span:first').replaceWith('<span>' + itemname + ' <span class="label label-important remove-item-label">X &nbsp;Remove</span></span>');

            if (points) {
                save_points($(item).find('input'), points);
            }

            category.append(item);
        }
    }

    // Add grade item
    $('button#add-item').click(function(e) {
        e.preventDefault();

        var category_name = get_selected('select#add-item-category');

        add_item(category_name, '');
    });

    function save_points(item, points) {
        var td = item.parent().parent();

        item.parent().remove();

        td.append('<span class="badge pull-right">' + points + ' Points</span>');
    }

    // Save item point value
    $('div.point-blank input').live('focusout', function(e) {
        var elem = $(e.currentTarget);
        var val = elem.val();

        save_points(elem, val);
    });

    // Category remove button
    $('span.remove-category-label').live('click', function(e) {
        var elem = $(e.currentTarget),
            name = elem.parent().find('span:first').html()

        elem.parent().parent().parent().parent().parent().remove();

        $('div.control-group[name="' + name + '"]').remove();
    });

    // Item remove button
    $('span.remove-item-label').live('click', function(e) {
        $(e.currentTarget).parent().parent().parent().remove();
    });

    // Hover to show remove button for categories
    $('tr').live({
        mouseenter: function(e) {
            $(e.currentTarget).children().find('span.remove-category-label').show();
        },
        mouseleave: function(e) {
            $(e.currentTarget).children().find('span.remove-category-label').hide();
        }
    });

    // Hover to show remove button for items
    $('tr').live({
        mouseenter: function(e) {
            $(e.currentTarget).children().find('span.remove-item-label').show();
        },
        mouseleave: function(e) {
            $(e.currentTarget).children().find('span.remove-item-label').hide();
        }
    });

    // Show or hide category weights
    $('select#grading-method').change(function(e) {
        var val = $('select#grading-method').val();

        if (val === "10") {
            $('form#category-weights').show();
        } else {
            $('form#category-weights').hide();
        }

    });

    // Convert form to JSON and Submit on save button click
    $('form#builder').submit(function() {
        var gb = {};

        gb['name'] = "New Template";
        gb['aggregation'] = $('select#grading-method').val();
        gb['categories'] = [];

        $('div#builder-start').find('table').each(function() {
            var cat_obj = {},
                parent = $(this),
                items = [];

            cat_obj['name'] = $(this).find('span:first').html();
            cat_obj['weight'] = 1;

            parent.find('td').each(function() {
                var gi_name = $(this).find('span:first').clone().children().remove().end().text().trim();
                var gi_points = $(this).find('span:last').html().split(' ')[0];

                items.push({
                    'name': gi_name,
                    'grademax': gi_points,
                    'itemtype': 'manual',
                });
            });

            cat_obj['items'] = items;

            gb['categories'].push(cat_obj);
        });

        $('input[name="data"]').val(JSON.stringify(gb));

        return true;
    });

    var gb_json = $('input[name="data"]').val();

    if (gb_json.length > 2) {
        var gb_obj = JSON.parse(gb_json);

        $.each(gb_obj['categories'], function() {
            var cat_node = this;

            add_category(cat_node.name);

            $.each(cat_node['items'], function() {
                add_item(cat_node.name, this.name, this.grademax);
            });
        });
    }
});
