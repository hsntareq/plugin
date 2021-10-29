var gulp = require("gulp"),
  sass = require("gulp-sass")(require("node-sass")),
  sourcemaps = require("gulp-sourcemaps"),
  rename = require("gulp-rename"),
  prefix = require("gulp-autoprefixer"),
  plumber = require("gulp-plumber"),
  notify = require("gulp-notify"),
  wpPot = require("gulp-wp-pot"),
  clean = require("gulp-clean"),
  zip = require("gulp-zip"),
  fs = require("fs"),
  path = require("path"),
  build_name = "sponsors-" + require("./package.json").version + ".zip";

var onError = function (err) {
  notify.onError({
    title: "Gulp",
    subtitle: "Failure!",
    message: "Error: <%= error.message %>",
    sound: "Basso",
  })(err);
  this.emit("end");
};

var prefixerOptions = {
  overrideBrowserslist: ["last 2 versions"],
};

var scss_blueprints = {
  sponsors_scss: {
    src: "assets/scss/sponsor.scss",
    mode: "expanded",
    destination: "sponsor.css",
  },
  sponsors_scss_min: {
    src: "assets/scss/sponsor.scss",
    mode: "compressed",
    destination: "sponsor.min.css",
  },
};

var task_keys = Object.keys(scss_blueprints);

for (let task in scss_blueprints) {
  let blueprint = scss_blueprints[task];
  var sassPaths = ["./node_modules"];

  gulp.task(task, function () {
    return gulp
      .src(blueprint.src)
      .pipe(plumber({ errorHandler: onError }))
      .pipe(sourcemaps.init({ loadMaps: true, largeFile: true }))
      .pipe(sass({ outputStyle: blueprint.mode, includePaths: sassPaths }))
      .pipe(prefix(prefixerOptions))
      .pipe(rename(blueprint.destination))
      .pipe(
        sourcemaps.write(".", { addComment: process.env._GULP_ENV != "build" })
      )
      .pipe(gulp.dest(blueprint.dest_path || "assets/css"));
  });
}

var added_texts = [];
const regex =
  /__\(\s*(['"])((?:(?!(?<!\\)\1).)+)\1(?:,\s*(['"])((?:(?!(?<!\\)\3).)+)\3)?\s*\)/gi;
const js_files = ["lib", "sponsor"]
  .map((f) => "assets/js/" + f + ".js:1")
  .join(", ");
function i18n_makepot(callback, target_dir) {
  const parent_dir = target_dir || __dirname;
  var translation_texts = "";

  // Loop through JS files inside js directory
  fs.readdirSync(parent_dir).forEach(function (file_name) {
    if (file_name == "node_modules" || file_name.indexOf(".") === 0) {
      return;
    }

    var full_path = parent_dir + "/" + file_name;
    var stat = fs.lstatSync(full_path);

    if (stat.isDirectory()) {
      i18n_makepot(null, full_path);
      return;
    }

    // Make sure only js extension file to process
    if (
      stat.isFile() &&
      path.extname(file_name) == ".js" &&
      full_path.indexOf("assets/scripts") > -1
    ) {
      var codes = fs.readFileSync(full_path).toString();
      var lines = codes.split("\n");

      // Loop through every single line in the JS file
      for (var i = 0; i < lines.length; i++) {
        var found = lines[i].match(regex);
        !Array.isArray(found) ? (found = []) : 0;

        // Loop through found translations
        for (var n = 0; n < found.length; n++) {
          // Parse the string

          var string = found[n];
          var first_quote = string.indexOf("'") + 1;
          var second_quote = string.indexOf("'", first_quote);
          var text = string.slice(first_quote, second_quote);

          if (added_texts.indexOf(text) > -1) {
            // Avoid duplicate entry
            continue;
          }

          added_texts.push(text);
          translation_texts +=
            "\n#: " + js_files + '\nmsgid "' + text + '"\nmsgstr ""' + "\n";
        }
      }
    }
  });

  // Finally append the texts to the pot file
  var text_domain = path.basename(__dirname);
  fs.appendFileSync(
    __dirname + "/languages/" + text_domain.toLowerCase() + ".pot",
    translation_texts
  );

  callback ? callback() : 0;
}

gulp.task("watch", function (watch) {
  gulp.watch("./**/*.scss", gulp.series(...task_keys));
  watch();
});

gulp.task("makepot", function () {
  return gulp
    .src("**/*.php")
    .pipe(
      plumber({
        errorHandler: onError,
      })
    )
    .pipe(
      wpPot({
        domain: "sponsors",
        package: "Sponsors Portal",
      })
    )
    .pipe(gulp.dest("languages/sponsor.pot"));
});

gulp.task("clean-zip", function () {
  return gulp
    .src("./" + build_name, {
      read: false,
      allowEmpty: true,
    })
    .pipe(clean());
});

gulp.task("clean-build", function () {
  return gulp
    .src("./build", {
      read: false,
      allowEmpty: true,
    })
    .pipe(clean());
});

gulp.task("copy", function () {
  return gulp
    .src([
      "./**/*.*",
      "!./build/**",
      "!./assets/**/*.map",
      "!./assets/src/**",
      "!./assets/scss/**",
      "!./assets/.sass-cache",
      "!./node_modules/**",
      "!./**/*.zip",
      "!.github",
      "!./readme.md",
      "!.DS_Store",
      "!./**/.DS_Store",
      "!./LICENSE.txt",
      "!./*.lock",
      "!./*.js",
      "!./*.json",
    ])
    .pipe(gulp.dest("build/sponsors/"));
});

gulp.task("make-zip", function () {
  return gulp.src("./build/**/*.*").pipe(zip(build_name)).pipe(gulp.dest("./"));
});

exports.build = gulp.series(
  ...task_keys,
  "clean-zip",
  "clean-build",
  "makepot",
  i18n_makepot,
  "copy",
  "make-zip"
);
exports.sass = gulp.series(...task_keys);
exports.default = gulp.series(...task_keys, "watch");
