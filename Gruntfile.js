module.exports = function(grunt) {
  // Initializing the configuration object
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    // Paths variables
    paths: {
      // Development file locations, where we put SASS files etc.
      src: {
        assets: './resources/assets/',
        css: '<%= paths.src.assets %>scss/',
        js: '<%= paths.src.assets %>js/'
      },
      // Production file locations, where Grunt will output the files
      dest: {
        assets: '<%= pkg.directories.lib %>assets/',
        css: '<%= paths.dest.assets %>css/',
        js: '<%= paths.dest.assets %>js/'
      }
    },

    // Task configuration
    // compile the SASS files
    sass: {
      options: {
        // @link https://github.com/sass/node-sass#outputstyle
        outputStyle: 'compressed',
        sourceMap: true
      },
      app: {
        files: {
          '<%= paths.dest.css %>plugin.css': '<%= paths.src.css %>plugin.scss'
        }
      }
    },
    watch: {
      grunt: { files: ['Gruntfile.js'] },

      sass: {
        files: '<%= paths.src.css %>**/*.scss',
        tasks: ['sass']
      }
    }
  });

  // Plugin loading
  grunt.loadNpmTasks('grunt-sass');
  grunt.loadNpmTasks('grunt-contrib-watch');

  /**
   * Task definition
   */
  // create the dev files (SASS etc.)
  grunt.registerTask('build', ['sass']);

  // watch for changes and run dev builds
  grunt.registerTask('default', ['build', 'watch']);
};
