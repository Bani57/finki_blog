const gulp = require('gulp')
const replace = require('gulp-replace')
const del = require('del')
const shell = require('gulp-shell')

const testing = {
  host: 'localhost',
  frontendBasePath: '/finki_blog/',
  backendBasePath: '/finki_blog/backend/',
  destDir: `/var/www/html/finki_blog`,
  database: 'blog'
}

const dev = {
  host: 'localhost',
  frontendBasePath: '/finki_blog/',
  backendBasePath: '/finki_blog/backend/',
  destDir: `/var/www/html/finki_blog`,
  database: 'blog'
}

const production = {
  host: 'localhost',
  frontendBasePath: '/finki_blog/',
  backendBasePath: '/finki_blog/backend/',
  destDir: `/var/www/html/finki_blog`,
  database: 'blog'
}

const srcDir = '.'

const databaseMarker = '@DATABASE' // will be replaced during deployment in PHP files
const docsHostMarker = '@DOCS_HOST' // will be replaced during deployment in JS files

// --------------- Build Frontend ---------------

gulp.task(
  'build-frontend-production',
  shell.task(`npm run build-prod`, {
    cwd: `${srcDir}/vue`
  })
)

gulp.task(
  'build-frontend-testing',
  shell.task(`npm run build-test`, {
    cwd: `${srcDir}/vue`
  })
)

gulp.task(
  'build-frontend-dev',
  shell.task(`npm run build`, {
    cwd: `${srcDir}/vue`
  })
)

// --------------- DEPLOY TO TEST ---------------

/**
 * @description Remove current frontend from testing server.
 */
gulp.task('clear-frontend-testing', () => {
  // return del([
  //   `${testing.destDir}/static`,
  //   `${testing.destDir}/index.html`
  // ], { force: true });
})

/**
 * @description Deploy frontend to testing server.
 */
gulp.task('deploy-frontend-testing', ['clear-frontend-testing'], () => {
  return gulp
    .src(`${srcDir}/vue/dist/**/*`)
    .pipe(gulp.dest(`${testing.destDir}/`))
})

/**
 * @description Remove current backend from testing server.
 */
gulp.task('clear-backend-testing', () => {
  // return del(`${testing.destDir}/backend/**`, { force: true });
})

/**
 * @description Deploy backend to testing server.
 */
gulp.task('deploy-backend-testing', ['clear-backend-testing'], () => {
  return gulp
    .src(`${srcDir}/backend/**`)
    .pipe(replace(databaseMarker, testing.database))
    .pipe(gulp.dest(`${testing.destDir}/backend/`))
})

/**
 * @description Build and deploy documentation to testing server.
 */
gulp.task('deploy-docs-testing', () => {
  return gulp
    .src(`${srcDir}/docs/**`)
    .pipe(replace(docsHostMarker, testing.host))
    .pipe(gulp.dest(`${testing.destDir}/docs/`))
})

// --------------- DEPLOY TO DEV ---------------

/**
 * @description Remove current frontend from dev server.
 */
gulp.task('clear-frontend-dev', () => {
  return del([
    `${dev.destDir}/static`,
    `${dev.destDir}/index.html`
  ], {
    force: true
  })
})

/**
 * @description Deploy frontend to dev server.
 */
gulp.task('deploy-frontend-dev', ['clear-frontend-dev'], () => {
  return gulp
    .src(`${srcDir}/vue/dist/**/*`)
    .pipe(gulp.dest(`${dev.destDir}/`))
})

/**
 * @description Remove current backend from dev server.
 */
gulp.task('clear-backend-dev', () => {
  return del(`${testing.destDir}/backend/**`, {
    force: true
  })
})

/**
 * @description Deploy backend to dev server.
 */
gulp.task('deploy-backend-dev', ['clear-backend-dev'], () => {
  return gulp
    .src(`${srcDir}/backend/**`)
    .pipe(replace(databaseMarker, dev.database))
    .pipe(gulp.dest(`${dev.destDir}/backend/`))
})

/**
 * @description Build and deploy documentation to testing server.
 */
gulp.task('deploy-docs-dev', () => {
  return gulp
    .src(`${srcDir}/docs/**`)
    .pipe(replace(docsHostMarker, dev.host))
    .pipe(gulp.dest(`${dev.destDir}/docs/`))
})

// --------------- DEPLOY TO PRODUCTION ---------------

/**
 * @description Remove current frontend from production server.
 */
gulp.task('clear-frontend-production', () => {
  return del([
    `${production.destDir}/static`,
    `${production.destDir}/index.html`
  ], {
    force: true
  })
})

/**
 * @description Deploy frontend to production server.
 */
gulp.task('deploy-frontend-production', ['clear-frontend-production'], () => {
  return gulp
    .src(`${srcDir}/vue/dist/**/*`)
    .pipe(gulp.dest(`${production.destDir}/`))
})

/**
 * @description Remove current backend from production server.
 */
gulp.task('clear-backend-production', () => {
  return del(`${production.destDir}/backend/**`, {
    force: true
  })
})

/**
 * @description Deploy backend to production server.
 */
gulp.task('deploy-backend-to-production', ['clear-backend-production'], () => {
  return gulp
    .src(`${srcDir}/backend/**`)
    .pipe(replace(databaseMarker, production.database))
    .pipe(replace(docsHostMarker, production.host))
    .pipe(gulp.dest(`${production.destDir}/backend/`))
})

/**
 * @description Build and deploy documentation to production server.
 */
gulp.task('deploy-docs-production', () => {
  return gulp
    .src(`${srcDir}/docs/**`)
    .pipe(replace(docsHostMarker, production.host))
    .pipe(gulp.dest(`${production.destDir}/docs/`))
})
