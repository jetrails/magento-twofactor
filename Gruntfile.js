module.exports = function ( grunt ) {

	// Import the package settings and the staging configuration
	var package = require ("./package.json");

	// Load NPM tasks so we can use them in our configuration
	grunt.loadNpmTasks ("grunt-contrib-clean");
	grunt.loadNpmTasks ("grunt-contrib-compress");
	grunt.loadNpmTasks ("grunt-contrib-watch");
	grunt.loadNpmTasks ("grunt-git");
	grunt.loadNpmTasks ("grunt-replace");
	grunt.loadNpmTasks ("grunt-mkdir");
	grunt.loadNpmTasks ("grunt-rsync");

	grunt.task.registerTask ( "default", "preform a full build", [
		"init",
		"resolve",
		"release"
	]);

	grunt.task.registerTask ( "deploy", "upload src contents to staging environment", function () {
		// Make sure that staging object is set in package JSON file
		if ( package.hasOwnProperty ( "staging" ) && package.staging.user.trim () != ""
		&& package.staging.host.trim () != "" && package.staging.port.trim () != ""
		&& package.staging.dest.trim () != "" ) {
			// Initialize the rsync option object
			var _rsync_options = {
				args: 		[ "-az", "-o", "-g", "-e 'ssh -p " + package.staging.port + "'" ],
				exclude: 	[],
				recursive: 	true
			}
			// Save it into grunt config
			grunt.config.set ( "rsync.options", _rsync_options );
			// Traverse through jetrails dependencies
			package.jetrailsDependencies.forEach ( function ( dependency ) {
				// Extract the dependency name
				var name = dependency.match (/\/([a-zA-Z0-9-]*)\.git$/) [ 1 ].toLowerCase ();
				// Initialize the rsync dependencies options
				var _rsync_dependencies = {
					src: 		"lib/" + name + "/src/*",
					dest: 		package.staging.dest,
					host: 		package.staging.user + "@" + package.staging.host
				}
				// Configure the dependency rsync options
				grunt.config.set ( "rsync." + name + ".options", _rsync_dependencies );
				// Run the dependencies task
				grunt.task.run ( "rsync:" + name + "" );
			});
			// Initialize the rsync staging options
			var _rsync_staging = {
				src: 		"src/*",
				dest: 		package.staging.dest,
				host: 		package.staging.user + "@" + package.staging.host
			}
			// Save it into grunt config
			grunt.config.set ( "rsync.staging.options", _rsync_staging );
			// Run the task and upload the main source files
			grunt.task.run ( "rsync:staging" );
		}
		// Otherwise, report that we cannot deploy
		else {
			grunt.log.writeln ( "could not find staging details in package.json" );
		}
	});

	grunt.task.registerTask ( "stream", "watch src and lib folders, deploy on change", function () {
		// Initialize the watch task options
		grunt.config.set ( "watch.module.files", [ "src/**/*", "lib/*/src/**/*" ] );
		grunt.config.set ( "watch.module.tasks", [ "deploy" ] );
		grunt.config.set ( "watch.module.options.spawn", false );
		// Run the task
		grunt.task.run ( "watch:module" );
	});

	grunt.task.registerTask ( "version", "update version, defined in package.json", function () {
		// Initialize the replacement patterns
		var _patterns = [
			{
				match: 				/^(\s*\*\s+@version\s+)([0-9]+\.[0-9]+\.[0-9]+)(\s*)$/gm,
				replacement: 		"$1" + package.version + "$3"
			},
			{
				match: 				/^(\s*<version>)([0-9]+\.[0-9]+\.[0-9]+)(<\/version>\s*)$/gm,
				replacement: 		"$1" + package.version + "$3"
			}
		];
		// Initialize which files are effected
		var _files = [
			{
				expand: 			true,
				flatten: 			false,
				src: 				[ "src/**/*.php", "src/**/*.phtml", "src/**/*.xml" ],
				dest: 				"."
			}
		];
		// Update the options in grunt config
		grunt.config.set ( "replace.version.options.patterns", _patterns );
		grunt.config.set ( "replace.version.files", _files );
		// Run the replace task
		grunt.task.run ( "replace:version" );
	});

	grunt.task.registerTask ( "init", "initialize file structure", function () {
		// If no arguments are passed set the default config
		if ( arguments.length == 0 ) {
			grunt.config.set ( "mkdir.execute.options.create", [ "src", "dist", "lib" ] );
			grunt.task.run ( "mkdir:execute" );
		}
		// Check to see if there is one argument
		else if ( arguments.length == 1 ) {
			grunt.config.set ( "mkdir.execute.options.create", [ arguments [ 0 ] ] );
			grunt.task.run ( "mkdir:execute" );
		}
		// Warn user if there is more than one argument
		else {
			grunt.log.writeln ( this.name + ": No argument or one argument is valid" );
		}
	});

	grunt.task.registerTask ( "nuke", "clear generated file structure", function () {
		// If no arguments are passed set the default config
		if ( arguments.length == 0 ) {
			grunt.config.set ( "clean.execute", [ "dist", "lib" ] );
			grunt.task.run ( "clean:execute" );
		}
		// Check to see if there is one argument
		else if ( arguments.length == 1 ) {
			var paths = [ arguments [ 0 ] + "/**", arguments [ 0 ] + "/.*" ];
			grunt.config.set ( "clean.execute", paths );
			grunt.task.run ( "clean:execute" );
		}
		// Warn user if there is more than one argument
		else {
			grunt.log.writeln ( this.name + ": No argument or one argument is valid" );
		}
	});

	grunt.task.registerTask ( "metadata", "initializes and replaces comment metadata", function () {
		// Loop through all the php files in source directory
		grunt.file.expand ("src/**/*.php").forEach ( function ( dir ) {
			// Extract the file name and the folder above it
			var filename = dir.split ("/") [ dir.split ("/").length - 1 ];
			var category = dir.split ("/") [ dir.split ("/").length - 2 ].toLowerCase ();
			filename = filename.replace ( ".php", "" );
			category = category.charAt ( 0 ).toUpperCase () + category.slice ( 1 );
			// Initialize the metadata header
			var header = "" +
				"\t * @version         " + package.version + "\n" +
				"\t * @package         " + package.company + " " + package.name + "\n" +
				"\t * @category        " + category + "\n" +
				"\t * @author          " + package.author + " - " + package.company + "\n";
			// If the package does not contain a copyright
			if ( package.copyright == null ) {
				// Append the license to it
				header += "\t * @license         " + package.license + "\n";
			}
			// Otherwise, apply the copyright
			else {
				// Append the copyright to the header
				header += "\t * @copyright       " + package.copyright + "\n";
			}
			// Initialize the replacement patterns
			var _patterns = [
				{
					match: 				/(\n[ \t]*\n[ \t]*)([ \t]+class [0-9a-zA-Z_]+(?: extends [0-9a-zA-Z_]+)?[ \t]*\{)/gm,
					replacement: 		"$1" + "\t/**\n" + "\t * " + filename + ".php - \n" + header + "\t */\n" + "$2"
				},
				{
					match: 				/\t\/\*\*\n\t \* [a-zA-Z]*\.php - ([a-zA-Z\W0-9\.\*]*?)\n\t \* @[a-zA-Z\W0-9\.\*]*\*\/\n\t(class [0-9a-zA-Z_]+(?: extends [0-9a-zA-Z_]+)?[ \t]*\{)/gm,
					replacement: 		"\t/**\n\t \* " + filename + ".php - $1\n" + header + "\t */\n" + "\t$2"
				},
				{
					match: 				/(\n[ \t]*\n)([ \t]*)([public|protected|private]+ function [a-zA-Z0-9_]+(?: )?\()([a-zA-Z0-9$_, ]*)(\)(?: )?\{)/gm,
					replacement: 		"$1 $2\/**\n$2 * \n$2 * @return\n$2 *\/\n" + "$2$3$4$5"
				}
			];
			// Initialize which files are effected
			var _files = [
				{
					expand: 			true,
					flatten: 			false,
					src: 				[ dir ],
					dest: 				"."
				}
			];
			// Define a temp variable for the grunt name (for async execution)
			var temp = category.replace ( /_|\.|-/gm, "" ) + filename.replace ( /_|-|\./g, "" );
			// Update the options in grunt config
			grunt.config.set ( "replace." + temp + ".options.patterns", _patterns );
			grunt.config.set ( "replace." + temp + ".files", _files );
			// Run the replace task
			grunt.task.run ( "replace:" + temp + "" );
		});
	});

	grunt.task.registerTask ( "resolve", "downloads jetrails dependency extensions", function () {
		// Run the dependency command
		grunt.task.run ( "init" );
		// Loop through all the dependencies
		package.jetrailsDependencies.forEach ( function ( dependency ) {
			// Extract the dependency name
			var name = dependency.match (/\/([a-zA-Z0-9-]*)\.git$/) [ 1 ].toLowerCase ();
			// Check to see if we already cloned the repository
			if ( grunt.file.exists ( "lib", name ) ) {
				// Nuke it
				grunt.task.run ( "nuke:lib" );
			}
			// Set the configuration, and clone it into lib
			grunt.task.run ( "init:lib/" + name );
			grunt.config.set ( "gitclone." + name + ".options.repository", dependency );
			grunt.config.set ( "gitclone." + name + ".options.directory", "lib/" + name );
			grunt.task.run ( "gitclone:" + name );
		});
	});

	grunt.task.registerTask ( "release", "prepares file structure for github release", function () {
		// Run the dependency tasks
		grunt.task.run ( "init" );
		grunt.task.run ( "resolve" );
		// Update the metadata in comments
		grunt.task.run ( "metadata" );
		// Change version numbers
		grunt.task.run ( "version" );
		// Clear dist folder
		grunt.task.run ( "nuke:dist" );
		// Initialize the files array for compression
		var files = [{
			cwd: 			"src",
			expand: 		true,
			src: 			["**"]
		}];
		// Traverse through jetrails dependencies
		package.jetrailsDependencies.forEach ( function ( dependency ) {
			// Extract the dependency name
			var name = dependency.match (/\/([a-zA-Z0-9-]*)\.git$/) [ 1 ].toLowerCase ();
			// Append to the files array
			files.push ({
				cwd: 			"lib/" + name + "/src",
				expand: 		true,
				src: 			["**"]
			});
		});
		// Define the output file
		var _output = package.name.replace ( "-", "_" ) + ".zip";
		// Define other options
		grunt.config.set ( "compress.module.options.archive", "dist/" + _output );
		grunt.config.set ( "compress.module.options.mode", "zip" );
		grunt.config.set ( "compress.module.files", files );
		// Compress the module
		grunt.task.run ( "compress:module" );
		// Nuke the lib folder
		grunt.task.run ( "nuke:lib" );
	});

};