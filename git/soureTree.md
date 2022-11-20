git remote update origin --pure //拉取/更新远程分支
git fetch --unshllow // 拉取履历
git checkout -b  dev // 创建并切换分支到 dev 分支
git checkout dev // 切换到dev分支
git push origin dev:dev // 将本地dev分支推送到远程仓库命名为dev （本地分支名:远程分支名）
git branch -d dev // 删除dev分支 
git push --delete origin dev // 删除远程分支dev
git branch 查看本地分支
git branch -r 查看远程分支
git reset HEAD 
git revert  安全的方式撤销提交
git stash push -m __file__ 加入暂存区

git log

git log --author=wangyangyang --no-decorate --name-only --after={2022-07-30} --no-merges --format="" | sort | uniq > 19.txt

git whatchanged --stat 


git log --name-status --since='..' --until='..' | grep -E '^[A-Z]\b' | sort | uniq | sed -e 's/^\w\t*\ *//'

svn log -v

