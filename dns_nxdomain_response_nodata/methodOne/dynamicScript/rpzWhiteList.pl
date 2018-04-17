#!/usr/bin/perl
=head
    File    : rpzWhiteList.pl
    Time    : 2018/04/17 02:25:10
    Author  : Guo,Xuan-Chen
=cut

# /* pragmas */ #
use strict;
use warnings;
use feature qw(say switch);
use Data::Dumper;
use Array::Diff;

# /* anaylze file to array (old & new) */ #
my $domainName = "skills.com";
my $rpzFile = "/etc/bind/security/db.rpz.whitelist";
my $domainFile = "/etc/bind/skills.com/db.forward";
my @oldFileValue = `cat $rpzFile | grep -E '^(\\w+)' | awk '{print \$1}' | sed "1d"`;
my @newFileValue = `cat $domainFile | grep -E '^(\\w)' | awk '{print \$1}'`;
my @oldArray = map({ chomp; $_ } @oldFileValue);
my @newArray = map({ chomp; "$_.skills.com"} @newFileValue);

# /* difference array (old & new) */ #
my $diff = Array::Diff->diff(\@oldArray, \@newArray);
my @addArray = @{$diff->added};
my @delArray = @{$diff->deleted};

# /* add RR */ #
if($#addArray != -1){
    foreach my $line (@addArray){
        my $writeValue = sprintf("%-20s\t%5s\t%17s", $line, "CNAME", "rpz-passthru.");
        `echo \"$writeValue\" >> $rpzFile`;
    }
}

# /* delete RR */ #
if($#delArray != -1){
    foreach my $line (@delArray){
        `sed -i '/$line/d' $rpzFile`;
    }
}
