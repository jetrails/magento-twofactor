module.exports = function ( grunt ) {

	// Import the package settings and the staging configuration
	var package = require ("./package.json");
	var staging = require ("./staging.json");

	// Initialize all the configurations for all the NPM tasks
	grunt.initConfig ({
		mkdir: {
			init: {
				options: {
					create: [ "src", "dist" ]
				},
			},
		},
		clean: {
			all: [ "dist/*", "doc/*" ]
		},
		replace: {
			version: {
				options: {
					patterns: [
						{
							match: /^(\s*\*\s+@version\s+)([0-9]+\.[0-9]+\.[0-9]+)(\s*)$/gm,
							replacement: "$1" + package.version + "$3"
						},
						{
							match: /^(\s*<version>)([0-9]+\.[0-9]+\.[0-9]+)(<\/version>\s*)$/gm,
							replacement: "$1" + package.version + "$3"
						}
					]
				},
				files: [
					{
						expand: true,
						flatten: false,
						src: [ "src/**/*" ],
						dest: "."
					}
				]
			},
		},
		rsync: {
			options: {
				args: [ "-az", "-o", "-g", "-e 'ssh -p 2223'" ],
				exclude: [],
				recursive: true
			},
			staging: {
				options: {
					src: "src/*",
					dest: staging.dest,
					host: staging.user + "@" + staging.host
				}
			}
		},
		compress: {
			module: {
				options: {
					archive: "dist/" + package.name.replace ( "-", "_" ) + ".zip",
					mode: "zip"
				},
				files: [{
					cwd: "src",
					expand: true,
					src: ["**"]
				}]
			}
		},
		watch: {
			module: {
				files: [ "src/**/*" ],
				tasks: [ "rsync:staging" ],
				options: {
					spawn: false
				}
			}
		}
	});

	// Load NPM tasks so we can use them in our tasks
	grunt.loadNpmTasks ("grunt-contrib-clean");
	grunt.loadNpmTasks ("grunt-contrib-compress");
	grunt.loadNpmTasks ("grunt-contrib-watch");
	grunt.loadNpmTasks ("grunt-replace");
	grunt.loadNpmTasks ("grunt-mkdir");
	grunt.loadNpmTasks ("grunt-rsync");

	// Define the default task
	grunt.task.registerTask ( "default", [
		"mkdir:init",
		"clean:all",
		"replace:version"
	]);

	// Define some other tasks
	grunt.task.registerTask ( "release", [
		"mkdir:init",
		"clean:all",
		"replace:version",
		"compress:module"
	]);

};