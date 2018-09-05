const gulp = require ("gulp")
const gzip = require ("gulp-gzip")
const magepack = require ("gulp-magepack")
const replace = require ("gulp-replace")
const tar = require ("gulp-tar")
const fs = require ("fs")
const fse = require ("fs-extra")

const PACKAGE_NAMESPACE = require ("./package.json").namespace
const PACKAGE_VERSION = require ("./package.json").version

const SOURCE_DIR = "src"
const BUILD_DIR = "build"
const STAGING_DIR = "public_html"
const PACKAGE_DIR = "dist"

gulp.task ( "init", [], ( callback ) => {
	let mkdirNotExists = ( name ) => {
		if ( !fs.existsSync ( name ) ) {
			fs.mkdirSync ( name )
		}
	}
	mkdirNotExists ( BUILD_DIR )
	mkdirNotExists ( PACKAGE_DIR )
	mkdirNotExists ( STAGING_DIR )
	callback ()
})

gulp.task ( "clean", [], ( callback ) => {
	let unlinkExists = ( name ) => {
		if ( fs.existsSync ( name ) ) {
			fse.removeSync ( name )
		}
	}
	unlinkExists ( BUILD_DIR )
	unlinkExists ( PACKAGE_DIR )
	callback ()
})

gulp.task ( "bump", [], ( callback ) => {
	return gulp.src ( SOURCE_DIR + "/**/*" )
		.pipe ( replace ( /(^.*\*\s+@version\s+)(.+$)/gm, "$1" + PACKAGE_VERSION ) )
		.pipe ( gulp.dest ( SOURCE_DIR ) )
		.on ( "done", callback )
})

gulp.task ( "build", [ "init" ], ( callback ) => {
	return gulp.src ( SOURCE_DIR + "/**/*" )
		.pipe ( gulp.dest ( BUILD_DIR ) )
		.on ( "done", callback )
})

gulp.task ( "deploy", ["build"], ( callback ) => {
	return gulp.src ( BUILD_DIR + "/**/*" )
		.pipe ( gulp.dest ( STAGING_DIR ) )
		.on ( "done", callback )
})

gulp.task ( "watch", ["deploy"], () => {
	return gulp.watch ( SOURCE_DIR + "/**/*", ["deploy"] )
})

gulp.task ( "package", [ "clean", "bump", "build" ], ( callback ) => {
	let options = {
		"template": "conf/package.xml",
		"version": PACKAGE_VERSION
	}
	gulp.src ( BUILD_DIR + "/**/*" )
		.pipe ( magepack ( options ) )
		.pipe ( tar (`${PACKAGE_NAMESPACE}-${PACKAGE_VERSION}`) )
		.pipe ( gzip ({ extension: "tgz" }) )
		.pipe ( gulp.dest ( PACKAGE_DIR ) )
		.on ( "done", callback )
})
