        // elastic revocations 
        $schedule->call(function () {
            $revoc_list = Revocation::where("id" , 1)->first();

            // remove authentication index
            $auth_revoc = $revoc_list->auth_revoc_period;
            $output = [];
            exec("curl -s -XGET 'http://localhost:9200/_cat/indices/auth*?h=index,creation.date.string' | awk '{print $0}'", $output);
            foreach ($output as $line) {
                $lines = explode(' ', $line);
                $tests = [];
                foreach ($lines as $line) {
                    if($line) $tests[] = $line;
                }
                $timesAgo = Carbon::parse($tests[1])->diffInDays();
                if($timesAgo > $auth_revoc) shell_exec('curl -XDELETE -k "http://localhost:9200/{{ $tests[0] }}"');
            }

        })->everySixHours();
