M.gradereport_gradebook_builder = {};

M.gradereport_gradebook_builder.init = function (Y, template_data) {
    // JSON template
    var template = Y.JSON.parse(template_data);

    // Populate interface
    this.build(template);

    // Register events
};

M.gradereport_gradebook_builder.build = function (template) {
    console.log(template);
};
