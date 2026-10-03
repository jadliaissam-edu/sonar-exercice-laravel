# Campus Helpdesk (Laravel) - SonarQube Practice Project

A small **support ticket / helpdesk** application (users, tickets, comments, attachments, reports) built with
**Laravel 11 / PHP 8.3**, exposed mainly as controllers and JSON API routes, backed by **SQLite** and tested with
**PHPUnit**.

It was written the way a busy team writes code over a few years: it works, the tests are green, but it is full of
code-quality problems. Your job in this lab is to **scan it with SonarQube, find the issues, understand why they matter
and fix them** - level by level.

> The application runs and all tests pass. Do not "fix" the project by deleting tests or excluding files.

---

## 1. Prerequisites

You need one of the two set-ups below, plus a running SonarQube (Community Build) at `http://localhost:9000` and
the `sonar-scanner` CLI (or the `sonarsource/sonar-scanner-cli` Docker image).

### Option A - local PHP

- PHP 8.3 with the extensions `pdo_sqlite`, `mbstring`, `openssl`, `curl`, `xml`, `zip`
- Composer 2
- A code-coverage driver: **PCOV** (recommended, fast) or **Xdebug 3**

### Option B - Docker (no PHP on your machine)

Only Docker is required. A ready-to-use image definition (PHP 8.3 + Composer + PCOV + SQLite) is in `docker/Dockerfile`.

```bash
docker build -t helpdesk-php83 -f docker/Dockerfile .
```

All commands below that start with `php`, `composer`, `vendor/bin/phpunit` or `sh scripts/coverage.sh` can be run
inside that image, for example:

```bash
docker run --rm -it -v "$PWD":/app helpdesk-php83 composer install
docker run --rm -it -v "$PWD":/app helpdesk-php83 php artisan migrate
```

(Windows PowerShell: replace `$PWD` by `${PWD}`.)

---

## 2. Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
```

Docker version of the same steps:

```bash
docker run --rm -v "$PWD":/app helpdesk-php83 sh -c "composer install && cp -n .env.example .env && php artisan key:generate && touch database/database.sqlite && php artisan migrate --seed"
```

> `composer.json` disables Composer's "block insecure packages" audit on purpose: this is a training project
> with an old framework line, and you only need it to install and run locally.

### Run the application

```bash
php artisan serve                      # http://127.0.0.1:8000
# Docker:
docker run --rm -it -p 8000:8000 -v "$PWD":/app helpdesk-php83 php artisan serve --host=0.0.0.0
```

Try it:

```bash
curl http://127.0.0.1:8000/api/tickets
curl -X POST http://127.0.0.1:8000/api/tickets \
     -H 'Accept: application/json' -H 'Content-Type: application/json' \
     -d '{"title":"Printer is jammed","priority":"high"}'
curl http://127.0.0.1:8000/api/stats/summary
```

The list of routes is in `routes/api.php` (and `routes/web.php`); `php artisan route:list` shows them all.

---

## 3. Tests and coverage

```bash
vendor/bin/phpunit                    # tests only
sh scripts/coverage.sh                # tests + coverage.xml (clover) + phpunit-report.xml (junit)
# Docker:
docker run --rm -v "$PWD":/app helpdesk-php83 sh scripts/coverage.sh
```

`scripts/coverage.sh` runs PHPUnit with `--coverage-clover coverage.xml --log-junit phpunit-report.xml` and then
rewrites the absolute paths inside both reports to **project-relative paths** (for example
`app/Level1/Controllers/TicketController.php`). This matters: if the reports contain paths such as `/app/app/...`
(Docker) or `/Users/me/...` (another machine), SonarQube cannot match them with your sources and you will see 0%
coverage. Always generate the reports with the script before scanning, and re-generate them after every change.

---

## 4. Scanning with SonarQube

1. In SonarQube create a project (or let the scanner create it) with key `jadli-laravel` and generate a
   **user token** (My Account > Security). Never write the token into a file that is committed to Git.
2. Run the tests with coverage (section 3).
3. Run the scanner from the project root:

```bash
export SONAR_TOKEN=<your-token>        # keep it in your shell only
sonar-scanner -Dsonar.token=$SONAR_TOKEN
```

The settings (sources, tests, exclusions, coverage report paths) live in `sonar-project.properties`.
Using Docker for the scanner (macOS/Windows):

```bash
docker run --rm -e SONAR_HOST_URL=http://host.docker.internal:9000 -e SONAR_TOKEN="$SONAR_TOKEN" \
    -v "$PWD":/usr/src sonarsource/sonar-scanner-cli
```

(On Linux add `--network host` and use `SONAR_HOST_URL=http://localhost:9000`.)

Blade views are excluded from the analysis (`sonar.exclusions`) because SonarQube's PHP analyzer parses every
`*.php` file, including `*.blade.php`, and Blade syntax is not valid PHP.

---

## 5. Levels and where to find them

Code is organised by difficulty: one namespace (= one folder under `app/`) per level. Everything belongs to the same
application, so the levels work together.

| Level | Theme | Namespace / folder | What lives there |
|-------|-------|--------------------|------------------|
| 1 - Beginner | Code smells | `App\Level1` (`app/Level1`) | Ticket listing / creation controllers, ticket service, formatting helpers, categories |
| 2 - Easy | Bugs and reliability | `App\Level2` (`app/Level2`) | Comments, assignment of tickets to agents, statistics |
| 3 - Intermediate | Vulnerabilities and security hotspots | `App\Level3` (`app/Level3`), `config/helpdesk.php` | Accounts and login, search, attachments, tokens, crypto, webhooks, middleware |
| 4 - Advanced | Maintainability, duplication, coverage, test quality | `App\Level4` (`app/Level4`), `tests/Unit/Level4` | SLA engine, reports, notifications, "ticket manager", test suite quality |
| 5 - Expert | Design problems, exceptions, global state, triage | `App\Level5` (`app/Level5`) | Workflow engine, domain model, access rules, supporting services |

The tests mirror this layout: `tests/Feature/LevelN` and `tests/Unit/LevelN`.

Tip: in SonarQube use the **Code** tab and open the folder of a level, or filter issues by
*Directory*. Start at Level 1 and move up when you are comfortable.

---

## 6. Exercises

Work in a Git branch. After each fix re-run the tests and the scan. Keep the behaviour of the API unchanged
unless an exercise says otherwise.

### Level 1 - Beginner (code smells)

1. Open the issues for `app/Level1` and group them by **rule**. How many different rules are there? Which are the most frequent?
2. Fix every issue about unused things (variables, parameters, private members, imports). Does SonarQube report all of the unused things you can find yourself? Why / why not?
3. Make the naming consistent with PSR-1/PSR-12 (classes, methods, constants, properties, parameters). What do you have to be careful about when you rename a class or a method in a Laravel project (hint: routes, container, autoloading)?
4. Remove dead, commented-out code and resolve the TODO / FIXME markers (decide for each: do it, ticket it, or delete it).
5. Find the duplicated literals and the redundant constructs (conditions that can be simplified, needless parentheses, boolean literals). Refactor them without changing behaviour.
6. Review the coding-style findings (file endings, whitespace, tabs, closing tags, modifier order, missing visibility). Is there a Laravel tool that can fix most of them automatically?
7. Find the debugging leftovers in `Formatter`. Is there a SonarQube rule for them? What would you use to prevent them from being committed?
8. Extra: look for magic numbers in `app/Level1`. Does the default profile report them? Discuss.

### Level 2 - Easy / Intermediate (bugs and reliability)

1. List the issues of type **Bug** in `app/Level2`. Sort by severity and explain, for each, what could go wrong at runtime.
2. Some bugs are only visible when input is missing or unusual. Find the code that assumes a record or an object always exists. Write a failing test first, then fix it (for the HTTP endpoints return a proper 404/422 instead of a 500).
3. Find the conditions that can never change the program flow (always true/false, duplicated operands, identical branches, unreachable statements).
4. Find the places where the result of a function call is thrown away, and the places where a variable is silently overwritten or an operator is mistyped.
5. Review the exception handling: which `catch` blocks can never run, which swallow errors?
6. Review the `switch` statements and loops (fall-through, missing default, references in `foreach`, loops that never repeat).
7. Compare `==` and `===` in this level: which comparisons are unsafe? Does SonarQube flag them? Discuss when a rule-based tool cannot help you.
8. Raise the coverage of `app/Level2` above 85% by writing meaningful tests (assertions on behaviour, not just execution).

### Level 3 - Intermediate (vulnerabilities and security hotspots)

1. Open the **Security Hotspots** and **Vulnerabilities** tabs for `app/Level3` and `config/`. Review **every** hotspot: mark it *Safe*, *Acknowledged* or *Fixed* and justify it in a comment. Goal: **100% hotspots reviewed**.
2. For each finding identify the OWASP Top 10 category the rule belongs to.
3. Replace weak hashing / random-number / encryption usage with the Laravel and PHP facilities designed for it (hint: `Hash`, `Str::random`, `random_int`, `Crypt`).
4. Remove every hard-coded credential, key and URL from the source. Where should these values come from? What must you do with the ones that were already committed?
5. Harden the HTTP side: CSRF protection, CORS, cookies, TLS verification for outgoing requests, clear-text protocols.
6. Harden the attachment upload and download (validation rules, storage disk, file names, size limits) and the account endpoints (mass assignment, password storage, logging).
7. Some dangerous constructs are *not* reported by the Community Build (they need taint analysis). Find at least three of them by code review (hint: look at what happens to `$request` data). Which SonarQube edition would report them?
8. Add tests for the code you fixed. Do not forget the negative cases (invalid uploads, wrong passwords, path traversal attempts).

### Level 4 - Advanced (maintainability, duplication, coverage, tests)

1. Open the **Measures** tab: which files have the highest cognitive complexity, the most duplicated lines, the lowest coverage? Draw a short "hot-spot map" of `app/Level4`.
2. Refactor the SLA evaluation so that no function exceeds the default cognitive-complexity threshold. Before you refactor, write characterisation tests (the current coverage of this class is 0%).
3. Remove the duplicated code between the two report controllers (and decide where the shared code should live).
4. Break down the very long method and the method with too many parameters. Which design patterns (value object, parameter object, query object, strategy) fit?
5. Review the big "manager" class. Should it be split? How would you decide?
6. Review the duplicated `switch` branches and nested ternaries in the notification code.
7. Review the test suite itself (`tests/Unit/Level4`): list every test smell (no assertions, skipped tests without reasons, sleeping tests, trivial assertions, wrong argument order, class naming, classes without tests). Fix them so that the suite is trustworthy.
8. **Quality Gate challenge:** create a branch, make changes in `app/Level4`, and make the *new code* pass a gate with: coverage on new code >= 80%, 0 new bugs, 0 new vulnerabilities, 100% of new security hotspots reviewed, duplicated lines on new code < 3%.

### Level 5 - Expert (realistic mixed capstone)

1. Review the class hierarchy under `app/Level5/Domain`. What is wrong with the depth of the inheritance tree? What other design problem is hidden in the base constructor?
2. Review `WorkflowService`: how many collaborators does it have? Which responsibilities can be extracted? Which ones need to be injected at all?
3. Review the exception handling in the workflow code (what is thrown, what is caught, what is swallowed, what happens in `finally`). Rewrite it with domain-specific exceptions and make the controller map them to HTTP responses.
4. Find all uses of global and static state. What breaks when tests run in a different order or in parallel?
5. Review the authorization rules (`AccessServiceProvider`). Are they strong enough? Write a test that proves your answer.
6. Review the modern PHP features used here (enums, readonly classes, constructor promotion). Are they used correctly? Write a test that exposes any misuse.
7. Look for error suppression, reflection tricks and useless overrides. What is the preferred replacement for each?
8. **Triage exercise:** In `app/Level5` there are findings that SonarQube is technically right about but that are **not** a problem in this context (one security hotspot, and one finding that may even be a false positive). Find them, decide *Safe* / *Won't Fix* / *False Positive* for each, and write the justification in SonarQube. Why is it important not to "fix" them blindly?
9. Raise the coverage of `app/Level5` above 90%, including branch coverage of the transition rules.

### Final challenge

Create a **new** project version / new-code period (for example "previous version"). Fix as many issues as you can in the time given and push the result through this Quality Gate:

| Condition (on new code) | Required |
|-------------------------|----------|
| Coverage | >= 80% |
| New bugs | 0 |
| New vulnerabilities | 0 |
| Security hotspots reviewed | 100% |
| Duplicated lines | < 3% |
| Maintainability rating | A |

---

## 7. Short SonarQube primer

- **Issue types (software qualities):** *Reliability* (bugs: code that is likely to fail or misbehave), *Security* (vulnerabilities: exploitable weaknesses) and *Maintainability* (code smells: code that is hard to understand or change). Newer versions call them *software qualities* with an *impact severity* (Blocker, High, Medium, Low, Info) in addition to the classic Blocker / Critical / Major / Minor / Info.
- **Security Hotspot:** security-sensitive code that needs a **human review**. It is not automatically a vulnerability. You review it and mark it *Safe*, *Acknowledged* or *Fixed*. "Reviewed %" is a Quality Gate metric.
- **Rule / Quality Profile:** a rule is one check (for example "Cognitive Complexity of functions should not be too high"); a Quality Profile is the set of activated rules for a language. The built-in profile is **Sonar way**.
- **Quality Gate:** a set of pass/fail conditions, usually on *new code*. The built-in "Sonar way" gate requires: no new issues of high impact (bugs / vulnerabilities), new hotspots reviewed 100%, new-code coverage >= 80%, duplication on new code <= 3%.
- **New code period:** the part of the code that counts as "new" (since a version, a number of days or a reference branch). "Clean as you code" means: keep the new code clean and the old code improves over time.
- **Coverage:** percentage of lines/conditions executed by tests. SonarQube does not run your tests; it **imports** the report (`coverage.xml`). No report = 0% coverage.
- **Duplications:** blocks of identical code of a minimum size (about 10 statements / 100 tokens for PHP). Measured as *duplicated lines (%)*.
- **Cognitive Complexity:** a measure of how hard a function is to understand (nesting and breaks in linear flow are penalised). Threshold in Sonar way: 15.
- **Technical debt / Maintainability rating:** estimated time to fix all code smells; rating A-E from the debt ratio.
- **Issue life-cycle and triage:** Open, Confirmed, Resolved as *False Positive* (the rule is wrong here) or *Won't Fix* (it is right but we accept it), Fixed. Always write a justification.
- **Community Build limits:** no taint analysis (injection / XSS / path traversal findings across method calls need Developer Edition or higher), no branch analysis or pull-request decoration (Developer Edition or higher).
- **Limits of static analysis:** a tool finds patterns, not intent. Some real defects are not reported, and some reported findings are harmless. Your judgement is part of the process.

---

## 8. Project layout

```
app/
  Level1/ ... Level5/        Controllers, Services, Support (and Middleware, Models, Domain, Enums, Providers)
  Models/                    Ticket, Comment, Category, User
bootstrap/app.php            Routing and middleware registration
config/helpdesk.php          Application specific settings
database/                    Migrations, factories, seeders
routes/                      api.php, web.php
tests/                       Feature/ and Unit/ (Level1 ... Level5)
scripts/coverage.sh          Tests + coverage + path normalisation
docker/Dockerfile            PHP 8.3 + Composer + PCOV image
sonar-project.properties     SonarQube scanner configuration
```
