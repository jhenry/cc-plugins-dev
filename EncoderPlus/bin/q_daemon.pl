#!/usr/bin/perl
use strict; # always!
use File::Queue;
my $q_num=$ARGV[0];
my $q_file="/var/local/spool/qdaemon/queue/$q_num";
my $q = new File::Queue (File => $q_file,Mode=>0774 );
print "Starting up $q_file ...\n";
$| = 1;
while (1) {
	my $elem1 = $q->deq(); 
	if ($elem1) {
		my $datestring = localtime();
		print "new job\n$elem1\n";
		print "Job Start time $datestring\n";
		print `$elem1`;
		$datestring = localtime();
		print "Job end $elem1\nqtime $datestring\n";

	} else {
		sleep 60;
	}
}
