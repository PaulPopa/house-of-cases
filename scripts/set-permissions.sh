user="${1:-www-data}"
group="${2:-www-data}"
dir="${3:-$( cd "$( dirname "${BASH_SOURCE[0]}" )" && cd .. && pwd )}"

find -L "$dir" -path ./vendor -prune -o -user root -print0 | xargs -0 -r chown $user
find -L "$dir" -path ./vendor -prune -o ! -group $group -print0 | xargs -0 -r chgrp $group
find -L "$dir" -path ./vendor -prune -o -user $user -type d -print0 | xargs -0 -r chmod 775
find -L "$dir" -path ./vendor -prune -o -user $user -type f -print0 | xargs -0 -r chmod 664
