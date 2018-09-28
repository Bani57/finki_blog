#!/bin/bash

GULP_DIR="."
API_DIR="./backend/api"

PURPLE='\033[1;35m'
YELLOW='\033[1;33m'
BLUE='\033[1;34m'
GREEN='\033[1;32m'
RED='\033[1;31m'
RESET='\033[0m'
BOLD='\033[1;37m'

BTTITLE="\n${BLUE}| ACCESS - deploy ${BOLD}BACKEND${BLUE} to ${GREEN}TESTING${BLUE} server ${RESET}"
BDTITLE="\n${BLUE}| ACCESS - deploy ${BOLD}BACKEND${BLUE} to ${GREEN}DEV${BLUE} server ${RESET}"
BPTITLE="\n${BLUE}| ACCESS - deploy ${BOLD}BACKEND${BLUE} to ${RED}PRODUCTION${BLUE} server ${RESET}"

FTTITLE="\n${BLUE}| ACCESS - deploy ${BOLD}FRONTEND${BLUE} to ${GREEN}TESTING${BLUE} server ${RESET}"
FDTITLE="\n${BLUE}| ACCESS - deploy ${BOLD}FRONTEND${BLUE} to ${GREEN}DEV${BLUE} server ${RESET}"
FPTITLE="\n${BLUE}| ACCESS - deploy ${BOLD}FRONTEND${BLUE} to ${RED}PRODUCTION${BLUE} server ${RESET}"

DTTITLE="\n${BLUE}| ACCESS - deploy ${BOLD}DOCUMENTATION${BLUE} to ${GREEN}TESTING${BLUE} server ${RESET}"
DDTITLE="\n${BLUE}| ACCESS - deploy ${BOLD}DOCUMENTATION${BLUE} to ${GREEN}DEV${BLUE} server ${RESET}"
DPTITLE="\n${BLUE}| ACCESS - deploy ${BOLD}DOCUMENTATION${BLUE} to ${RED}PRODUCTION${BLUE} server ${RESET}"

clear

function check_php_code {
    TO_EXIT=false

    while IFS= read -r -d $'\0'; do
        MESSAGE=$(php -l $REPLY)
        if [[ $MESSAGE = *"Parse error"* ]] ; then
            printf "${RED}${MESSAGE}${RESET}\n\n"
            TO_EXIT=true
        else
            printf "${GREEN}${MESSAGE}${RESET}\n"
        fi
    done < <(find $API_DIR -name \*.php -print0)

    if [[ $TO_EXIT = true ]] ; then
        printf "\n${RED}| Fix the code errors first. ${RESET}\n\n"
        exit 1
    fi
}

if [[ $1 == "--backend" ]] && [[ $2 == '--testing' ]]; then
    printf "${BTTITLE}\n"

    printf "\n${PURPLE}>_ Checking code... ${RESET}\n"
    check_php_code

    printf "\n${PURPLE}>_ Deploying... ${RESET}\n"
    ( cd $GULP_DIR ; gulp deploy-backend-testing )

    printf "\n${GREEN}| Done. ${RESET}\n\n"
elif [[ $1 == "--backend" ]] && [[ $2 == '--dev' ]]; then
    printf "${BDTITLE}\n"

    printf "\n${PURPLE}>_ Checking code... ${RESET}\n"
    check_php_code

    printf "\n${PURPLE}>_ Deploying... ${RESET}\n"
    ( cd $GULP_DIR ; gulp deploy-backend-dev )

    printf "\n${GREEN}| Done. ${RESET}\n\n"
elif [[ $1 == "--backend" ]] && [[ $2 = '--production' ]]; then
    printf "${BPTITLE}in ${GREEN}5${RESET} " && sleep 1 && clear
    printf "${BPTITLE}in ${GREEN}4${RESET} " && sleep 1 && clear
    printf "${BPTITLE}in ${RED}3${RESET} " && sleep 1 && clear
    printf "${BPTITLE}in ${RED}2${RESET} " && sleep 1 && clear
    printf "${BPTITLE}in ${RED}1${RESET} " && sleep 1 && clear
    printf "${BPTITLE}\n"

    printf "\n${PURPLE}>_ Checking code... ${RESET}\n"
    check_php_code

    printf "\n${PURPLE}>_ Deploying... ${RESET}\n"
    ( cd $GULP_DIR ; gulp deploy-backend-to-production )

    printf "\n${GREEN}| Done. ${RESET}\n\n"
elif [[ $1 == "--frontend" ]] && [[ $2 = '--testing' ]]; then
    printf "${FTTITLE}\n"

    printf "\n${PURPLE}>_ Building for testing... ${RESET}\n"
    ( cd $GULP_DIR ; gulp build-frontend-testing )

    printf "\n${PURPLE}>_ Deploying... ${RESET}\n"
    ( cd $GULP_DIR ; gulp deploy-frontend-testing )

    printf "\n${GREEN}| Done. ${RESET}\n\n"
elif [[ $1 == "--frontend" ]] && [[ $2 = '--dev' ]]; then
    printf "${FDTITLE}\n"

    printf "\n${PURPLE}>_ Building for testing... ${RESET}\n"
    ( cd $GULP_DIR ; gulp build-frontend-dev )

    printf "\n${PURPLE}>_ Deploying... ${RESET}\n"
    ( cd $GULP_DIR ; gulp deploy-frontend-dev )

    printf "\n${GREEN}| Done. ${RESET}\n\n"
elif [[ $1 == "--frontend" ]] && [[ $2 = '--production' ]]; then
    printf "${FPTITLE}in ${GREEN}5${RESET} " && sleep 1 && clear
    printf "${FPTITLE}in ${GREEN}4${RESET} " && sleep 1 && clear
    printf "${FPTITLE}in ${RED}3${RESET} " && sleep 1 && clear
    printf "${FPTITLE}in ${RED}2${RESET} " && sleep 1 && clear
    printf "${FPTITLE}in ${RED}1${RESET} " && sleep 1 && clear
    printf "${FPTITLE}\n"

    printf "\n${PURPLE}>_ Building for production... ${RESET}\n"
    ( cd $GULP_DIR ; gulp build-frontend-production )

    printf "\n${PURPLE}>_ Deploying... ${RESET}\n"
    ( cd $GULP_DIR ; gulp deploy-frontend-production )

    printf "\n${GREEN}| Done. ${RESET}\n\n"
elif [[ $1 == "--docs" ]] && [[ $2 = '--testing' ]]; then
    printf "${DTTITLE}\n"

    printf "\n${PURPLE}>_ Building documentation... ${RESET}\n"
    # swagger "backend/api" --output "docs/api.json"
    ~/.composer/vendor/bin/swagger "backend/api" --output "docs/api.json"

    printf "${PURPLE}>_ Deploying... ${RESET}\n"
    ( cd $GULP_DIR ; gulp deploy-docs-testing )

    printf "\n${GREEN}| Done. ${RESET}\n\n"
elif [[ $1 == "--docs" ]] && [[ $2 = '--dev' ]]; then
    printf "${DDTITLE}\n"

    printf "\n${PURPLE}>_ Building documentation... ${RESET}\n"
    # swagger "backend/api" --output "docs/api.json"
    ~/.composer/vendor/bin/swagger "backend/api" --output "docs/api.json"

    printf "${PURPLE}>_ Deploying... ${RESET}\n"
    ( cd $GULP_DIR ; gulp deploy-docs-dev )

    printf "\n${GREEN}| Done. ${RESET}\n\n"
elif [[ $1 == "--docs" ]] && [[ $2 = '--production' ]]; then
    printf "${DPTITLE}in ${GREEN}5${RESET} " && sleep 1 && clear
    printf "${DPTITLE}in ${GREEN}4${RESET} " && sleep 1 && clear
    printf "${DPTITLE}in ${RED}3${RESET} " && sleep 1 && clear
    printf "${DPTITLE}in ${RED}2${RESET} " && sleep 1 && clear
    printf "${DPTITLE}in ${RED}1${RESET} " && sleep 1 && clear
    printf "${DPTITLE}\n"

    printf "\n${PURPLE}>_ Building documentation... ${RESET}\n"
    ~/.composer/vendor/bin/swagger "backend/api/$3" --output "docs/api.json"

    printf "${PURPLE}>_ Deploying... ${RESET}\n"
    ( cd $GULP_DIR ; gulp deploy-docs-production )

    printf "\n${GREEN}| Done. ${RESET}\n\n"
else
    printf "\n${PURPLE}| ACCESS - deployment script ${RESET}\n"
    printf "\n${BLUE}| Available options: ${RESET}"
    printf "\n${BLUE}|${BOLD} --frontend ( ${GREEN}--testing${BOLD} | ${RED}--dev${BOLD} | ${RED}--production${BOLD} ) ${RESET}"
    printf "\n${BLUE}|${BOLD} --backend  ( ${GREEN}--testing${BOLD} | ${RED}--dev${BOLD} | ${RED}--production${BOLD} ) ${RESET}"
    printf "\n${BLUE}|${BOLD} --docs     ( ${GREEN}--testing${BOLD} | ${RED}--production${BOLD} ) ${RESET}\n\n"
fi
