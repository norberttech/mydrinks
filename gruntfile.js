module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        appDir: 'public/assets',
        builtDir: 'public/assets/compiled',
        
        clean: {
            build: ["<%= builtDir %>"]
        },
        requirejs: {
            main: {
                options: {
                    mainConfigFile: '<%= appDir %>/js/common.js',
                    appDir: '<%= appDir %>',
                    baseUrl: './js',
                    dir: '<%= builtDir %>',
                    optimizeCss: "none",
                    optimize: "none"
                }
            }
        },
        uglify: {
            options: {
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
            },
            build: {
                files: [
                    {
                        expand: true,
                        cwd: '<%= builtDir %>',
                        src: 'js/*.js',
                        dest: '<%= builtDir %>'
                    },
                    {
                        expand: true,
                        cwd: '<%= builtDir %>',
                        src: 'js/app/page/**/*.js',
                        dest: '<%= builtDir %>'
                    },
                    {
                        expand: true,
                        cwd: '<%= builtDir %>',
                        src: 'js/app/component/**/*.js',
                        dest: '<%= builtDir %>'
                    }
                ]
            }
        },
        copy: {
            development: {
                files: [
                    // includes files within path
                    {
                        expand: true,
                        src: ['<%= appDir %>/vendor/bootstrap/fonts/*'],
                        dest: '<%= appDir %>/fonts', filter: 'isFile',
                        flatten: true
                    }
                ]
            },
            production: {
                files: [
                    // includes files within path
                    {
                        expand: true, 
                        src: ['<%= appDir %>/vendor/bootstrap/fonts/*'], 
                        dest: '<%= builtDir %>/fonts', filter: 'isFile',
                        flatten: true
                    }
                ]
            }
        },
        less: {
            development: {
                files: {
                    "<%= appDir %>/css/layout.css": "<%= appDir %>/less/layout.less",
                    "<%= appDir %>/css/search.css": "<%= appDir %>/less/search.less",
                    "<%= appDir %>/css/admin.css": "<%= appDir %>/less/admin.less",
                    "<%= appDir %>/css/recipe.css": "<%= appDir %>/less/recipe.less"
                }
            },
            production: {
                options: {
                    cleancss: true
                },
                files: {
                    "<%= builtDir %>/css/layout.css": "<%= appDir %>/less/layout.less",
                    "<%= builtDir %>/css/search.css": "<%= appDir %>/less/search.less",
                    "<%= builtDir %>/css/admin.css": "<%= appDir %>/less/admin.less",
                    "<%= appDir %>/css/recipe.css": "<%= appDir %>/less/recipe.less"
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-requirejs');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-copy');
    
    grunt.registerTask('default', ['less:development', 'copy:development']);
    grunt.registerTask('production', ['clean', 'requirejs', 'uglify', 'copy:production', 'less:production']);
};
