<?php

return [
    /*
    |-------------------------------------------------------------------------
    | Logging
    |-------------------------------------------------------------------------
    |
    | This package generates log for 04 scenarios:
    | - Start of import
    | - End of import
    | - Import failed
    | - Data validation inconsistencies/failures
    |
    | If logging is true, all the above scenarios will be logged in the log
    | using the format defined in the application.
    | This is useful to monitor if the import process is proceeding as
    | expected.
    | If false, will not log for the first two scenarios, but will continue for
    | others as they are not affected by this setting.
    |
    */

    'logging' => true,

    /*
    | ------------------------------------------------------------------------
    | Max Upsert
    | ------------------------------------------------------------------------
    |
    | Default number of objects that will be persisted in the database per
    | query.
    |
    | If less than or equal to zero, the package will assume the default value
    | defined internally.
    |
    */
    'maxupsert' => 500,
];
