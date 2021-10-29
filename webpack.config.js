const path = require("path");
const fs = require("fs");
const TerserPlugin = require("terser-webpack-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");

module.exports = (env, options) => {
  const mode = options.mode || "development";

  const config = {
    mode,
    module: {
      rules: [
        {
          test: /\.js$/,
          exclude: /node_modules/,
          use: "babel-loader",
        },
      ],
    },
    devtool: "source-map",
  };

  if ("production" === mode) {
    var minimizer =
      env != "build"
        ? new TerserPlugin({
            terserOptions: {},
            minify: (file) => {
              const uglifyJsOptions = {
                sourceMap: true,
              };
              return require("uglify-js").minify(file, uglifyJsOptions);
            },
          })
        : new TerserPlugin({
            terserOptions: {},
            minify: (file) => {
              const uglifyJsOptions = {
                sourceMap: false,
              };
              return require("uglify-js").minify(file, uglifyJsOptions);
            },
          });

    config.devtool = false;
    config.optimization = {
      minimize: true,
      minimizer: [minimizer, new CssMinimizerPlugin()],
    };
  }

  // Get react blueprints
  const assets_dir = __dirname + "/assets";
  var scripts_blueprints = [
    {
      dest_path: "./assets/js",
      scripts_files: {
        lib: "./assets/scripts/libs.js",
        sponsor: "./assets/scripts/sponsor.js",
      },
    },
  ];

  fs.readdirSync(assets_dir).forEach(function (dir_name) {
    var scripts_dir = assets_dir + "/" + dir_name + "/assets/scripts";

    if (
      !fs.existsSync(scripts_dir) ||
      !fs.lstatSync(scripts_dir).isDirectory()
    ) {
      // scripts directory not found
      return;
    }

    var blueprint = {
      dest_path: "./assets/js",
      scripts_files: {},
    };

    fs.readdirSync(scripts_dir).forEach(function (file_name) {
      var file = scripts_dir + "/" + file_name;
      var stat = fs.lstatSync(file);

      if (!stat.isFile() || path.extname(file_name) != ".js") {
        // Not react js files
        return;
      }

      var basename = path.parse(file).name;

      blueprint.scripts_files[basename] = "./assets/scripts/" + file_name;
    });

    if (Object.keys(blueprint.scripts_files).length) {
      scripts_blueprints.push(blueprint);
    }
  });

  var configEditors = [];
  for (let i = 0; i < scripts_blueprints.length; i++) {
    let { scripts_files, dest_path } = scripts_blueprints[i];

    configEditors.push(
      Object.assign({}, config, {
        name: "configEditor",
        entry: scripts_files,
        output: {
          path: path.resolve(dest_path),
          filename: `[name].js`,
        },
      })
    );
  }

  var files = [].concat(
    ...scripts_blueprints.map((m) => {
      return Object.keys(m.scripts_files).map((f) => {
        return m.dest_path + "/" + f + ".js";
      });
    })
  );

  fs.writeFileSync(
    __dirname + "/sponsors.json",
    JSON.stringify({ js_files: files })
  );

  return [...configEditors];
};
