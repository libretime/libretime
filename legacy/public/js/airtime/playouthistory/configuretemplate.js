var AIRTIME = (function (AIRTIME) {
  var mod;
  var $templateDiv;
  var $templateList;
  var $fileMDList;

  if (AIRTIME.itemTemplate === undefined) {
    AIRTIME.itemTemplate = {};
  }
  mod = AIRTIME.itemTemplate;

  //config: name, type, filemd, required
  function createTemplateLi(config) {
    var templateRequired =
      "<li " +
      "data-name='<%= name %>' " +
      "data-type='<%= type %>' " +
      "data-filemd='<%= filemd %>'" +
      "data-label='<%= label %>'" +
      "class='<%= (filemd) ? 'field_filemd' : 'field_other'  %>'" +
      ">" +
      "<span><%= label %></span>" +
      "<span><%= type %></span>" +
      "</li>";

    var templateOptional =
      "<li " +
      "data-name='<%= name %>' " +
      "data-type='<%= type %>' " +
      "data-filemd='<%= filemd %>'" +
      "data-label='<%= label %>'" +
      "class='<%= (filemd) ? 'field_filemd' : 'field_other'  %>'" +
      ">" +
      "<span><%= label %></span>" +
      "<span><%= type %></span>" +
      "<span class='template_item_remove'><i class='icon icon-trash'></i></span>" +
      "</li>";

    var template =
      config.required === true ? templateRequired : templateOptional;

    template = _.template(template);
    var $li = $(template(config));

    return $li;
  }

  //taken from
  //http://stackoverflow.com/questions/1349404/generate-a-string-of-5-random-characters-in-javascript
  function randomString(len, charSet) {
    //can only use small letters to avoid DB query problems.
    charSet = charSet || "abcdefghijklmnopqrstuvwxyz";
    var randomString = "";
    for (var i = 0; i < len; i++) {
      var randomPoz = Math.floor(Math.random() * charSet.length);
      randomString += charSet.substring(randomPoz, randomPoz + 1);
    }
    return randomString;
  }

  function addField(config) {
    $templateList.append(createTemplateLi(config));
  }

  function getFieldData($el) {
    return {
      name: $el.data("name"),
      type: $el.data("type"),
      label: $el.data("label"),
      isFileMd: $el.data("filemd"),
    };
  }

  mod.onReady = function () {
    $templateDiv = $("#configure_item_template");
    $templateList = $(".template_item_list");
    $fileMDList = $(".template_file_md");

    $fileMDList.on("click", "i.icon-plus", function () {
      var $li = $(this).parents("li");
      var config = {
        name: $li.data("name"),
        type: $li.data("type"),
        label: $li.data("label"),
        filemd: true,
        required: false,
      };

      addField(config);
      $li.remove();
    });

    $templateList.sortable();

    $templateDiv.on("click", ".template_item_remove", function () {
      $(this).parents("li").remove();
    });

    $templateDiv.on("click", ".template_item_add button", function () {
      var $div = $(this).parents("div.template_item_add"),
        $input = $div.find("input"),
        label = $input.val(),
        name;

      $input.val("");
      //create a string name that will work for all languages.
      name = randomString(10);

      var config = {
        name: name,
        label: label,
        type: $div.find("select").val(),
        filemd: false,
        required: false,
      };

      addField(config);
    });

    function updateTemplate(template_id, isDefault) {
      var url = baseUrl + "Playouthistorytemplate/update-template/format/json";
      var data = {};
      var $lis, $li;
      var i, len;
      var templateName;

      templateName = $("#template_name").val();
      $lis = $templateList.children();

      for (i = 0, len = $lis.length; i < len; i++) {
        $li = $($lis[i]);

        data[i] = getFieldData($li);
      }

      $.post(
        url,
        {
          id: template_id,
          name: templateName,
          fields: data,
          setDefault: isDefault,
        },
        function (json) {
          var x;
        },
      );
    }

    $templateDiv.on("click", "#template_item_save", function () {
      var template_id = $(this).data("template");

      updateTemplate(template_id, false);
    });

    $templateDiv.on("click", "#template_set_default", function () {
      var $btn = $(this),
        template_id = $btn.data("template"),
        url =
          baseUrl + "Playouthistorytemplate/set-template-default/format/json";

      $btn.remove();
      $.post(url, { id: template_id });
    });
  };

  return AIRTIME;
})(AIRTIME || {});

$(document).ready(AIRTIME.itemTemplate.onReady);
