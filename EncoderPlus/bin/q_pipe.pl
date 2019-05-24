#!/usr/bin/perl
use strict; # always!
use File::Queue;
my $job=$ARGV[2];
$job =~ s/--video=// ;
my $q_num=$job % 7;
my $q_file="/var/local/spool/qdaemon/queue/$q_num";
my $q = new File::Queue (File => $q_file,Mode=>0774 );
my $command=join(' ',@ARGV);
print "Job $job Q $q_num\n";
print "command $command \n";
$q->enq($command);
exit;
