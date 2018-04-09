module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		sass: {
			dist: {
				options: {
					style: 'compressed'
				},
				files: {
					'./css/style.css':'./sassfiles/main-style.scss'
				}
			}
		},
		postcss: {
			options: {
				map: {
					inline: false,
					annotation: './css/map'
				},

				processors: [
					require('pixrem')(),
					require('autoprefixer')({browsers: 'last 2 versions'}), 
					require('cssnano')()
				]
			},
			dist: {
				src: './css/*.css'
			}
		},
		watch: {
			css: {
				files: ['./sassfiles/**/*.scss'],
				tasks: ['sass', 'postcss'],
				options: {
					livereload: false,
				},
			},
		}
	});

	// Load the plugin that provides the "uglify" task.
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-postcss');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Default task(s).
	grunt.registerTask('default', ['watch']);

};